<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Form\Field;

use FOF30\Form\Field\Text as FieldBase;
use FOF30\Model\DataModel;
use \JLanguageAssociations as LanguageAssociations;
use \JRoute as Route;
use \JHtml as Html;

/**
 * Association Field class for F0F
 */
class Associations extends FieldBase
{
	/**
	 * @inheritdoc
	 */
	public function getInput()
	{
		if (!LanguageAssociations::isEnabled())
		{
			return 'Association plugin disabled';
		}

		if (!$this->item instanceof DataModel)
		{
			$this->item = $this->form->getModel();
		}

		$extension = isset($this->element['extension']) ? $this->element['extension'] : $this->form->getContainer()->componentName;
		$context   = isset($this->element['context']) ? $this->element['context'] : $this->item->getContentType();
		$title     = isset($this->element['titleField']) ? $this->element['titleField'] : $this->item->getFieldAlias('title');
		$model     = isset($this->element['model']) ? $this->element['model'] : $this->item->getName();

		$dbName = $this->item->getTableName();
		$pk     = $this->item->getKeyName();
		$id     = $this->item->$pk;

		$langField = $this->item->getFieldAlias('language');
		$lang      = $this->item->$langField;
		$languages = \JLanguageHelper::getLanguages('lang_code');

		$html = '';

		$associations = LanguageAssociations::getAssociations($extension, $dbName, $context, $id, $pk, null);

		foreach ($languages as $tag => $language)
		{
			if (empty($lang) || $tag != $lang)
			{
				$field = new \SimpleXMLElement('<field />');

				$field->addAttribute('name', 'associations[' . $tag . ']');
				$field->addAttribute('type', 'model');
				$field->addAttribute('label', $language->title);
				$field->addAttribute('model', $model);
				$field->addAttribute('key_field', $pk);
				$field->addAttribute('value_field', $title);
				$option = $field->addChild('option');
				$state  = $field->addChild('state', $tag);
				$state->addAttribute('key', 'language');

				$aId = isset($associations[$tag]->$pk) ? $associations[$tag]->$pk : null;

				$modelField = new \FOF30\Form\Field\Model($this->form);
				$modelField->item = $this->item;
				$modelField->setup($field, $aId);

				$html .= $modelField->renderField();
			}
		}

		return $html;
	}

	/**
	 * @inheritdoc
	 */
	public function getRepeatable()
	{
		if (!LanguageAssociations::isEnabled())
		{
			return '';
		}

		$extension = isset($this->element['extension'])  ? $this->element['extension']  : $this->form->getContainer()->componentName;
		$context   = isset($this->element['context'])    ? $this->element['context']    : $this->item->getContentType();
		$url       = isset($this->element['url'])        ? $this->element['url']        : null;
		$titleName = isset($this->element['titleField']) ? $this->element['titleField'] : $this->item->getFieldAlias('title');

		$dbName = $this->item->getTableName();
		$pk     = $this->item->getKeyName();
		$id     = $this->item->$pk;

		$html = '';

		// Get the associations
		if ($associations = LanguageAssociations::getAssociations($extension, $dbName, $context, $id, $pk, null))
		{
			foreach ($associations as $tag => $associated)
			{
				$associations[$tag] = (int) $associated->$pk;
			}

			// Get the associated menu items
			$db = $this->item->getDbo();
			$query = $db->getQuery(true)
				->select('c.' . $db->qn($pk) . ' AS id, c.' . $db->qn($titleName) . ' AS title')
				->select('l.sef as lang_sef')
				->from($db->qn($dbName) . ' as c')
				->select('cat.title as category_title')
				->join('LEFT', '#__categories as cat ON cat.id=c.catid')
				->where('c.' . $db->qn($pk) . ' IN (' . implode(',', array_values($associations)) . ')')
				->join('LEFT', '#__languages as l ON c.language=l.lang_code')
				->select('l.image')
				->select('l.title as language_title');
			$db->setQuery($query);

			try
			{
				$items = $db->loadObjectList();
			}
			catch (\RuntimeException $e)
			{
				throw new \Exception($e->getMessage(), 500);
			}

			if ($items)
			{
				foreach ($items as &$item)
				{
					$text = strtoupper($item->lang_sef);
					$view = $this->form->getContainer()->input->getCmd('view', 'item');

					if (isset($url))
						$url = vsprintf($url, array($extension, $view, (int) $item->id));
					else
						$url = Route::_('index.php?option=' . $extension . '&view=' . $view . '&task=edit&id=' . (int) $item->id);

					$tooltipParts = array(
						Html::_('image', 'mod_languages/' . $item->image . '.gif',
							$item->language_title,
							array('title' => $item->language_title),
							true
						),
						$item->title,
						'(' . $item->category_title . ')'
					);
					$item->link = Html::_('tooltip', implode(' ', $tooltipParts), null, null, $text, $url, null, 'hasTooltip label label-association label-' . $item->lang_sef);
				}
			}

			$html = \JLayoutHelper::render('joomla.content.associations', $items);
		}

		return $html;
	}
}
