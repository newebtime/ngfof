<?php
/**
 * @package    Ngfof
 * @copyright  Copyright (c) 2013-2015 Frédéric Vandebeuque / Newebtime.com
 * @license    GNU General Public License version 3, or later
 */

namespace Ngfof\Render;

use FOF30\Container\Container;
use FOF30\Form\Form;
use FOF30\Form\Field\Ordering as FieldOrdering;

use FOF30\Form\Header\Ordering as HeaderOrdering;
use FOF30\Model\DataModel;
use FOF30\Render\AkeebaStrapper as RenderBase;
use FOF30\Render\RenderInterface;

/**
 * BS3 view renderer class.
 */
class Fulldiv extends RenderBase implements RenderInterface
{
	public function __construct(Container $container)
	{
		$this->priority	 = 59;
		$this->enabled	 = 1;

		parent::__construct($container);
	}

	/**
	 * Renders a Form for a Browse view and returns the corresponding HTML
	 *
	 * @param   Form   &$form  The form to render
	 * @param   DataModel  $model  The model providing our data
	 *
	 * @return  string    The HTML rendering of the form
	 */
	public function renderFormBrowse(Form &$form, DataModel $model)
	{
		$html = '';

		\JHtml::_('behavior.multiselect');

		\JHtml::_('bootstrap.tooltip');
		\JHtml::_('dropdown.init');
		$view	 = $form->getView();
		$order	 = $view->escape($view->getLists()->order);

		$html .= <<<HTML
<script type="text/javascript">
	Joomla.orderTable = function() {
		var table = document.getElementById("sortTable");
		var direction = document.getElementById("directionTable");
		var order = table.options[table.selectedIndex].value;
		var dirn = 'asc';

		if (order != '$order')
		{
			dirn = 'asc';
		}
		else {
			dirn = direction.options[direction.selectedIndex].value;
		}

		Joomla.tableOrdering(order, dirn);
	};
</script>

HTML;

		// Getting all header row elements
		$headerFields = $form->getHeaderset();

		// Get form parameters
		$show_header		 = $form->getAttribute('show_header', 1);
		$show_filters		 = $form->getAttribute('show_filters', 1);
		$show_pagination	 = $form->getAttribute('show_pagination', 1);
		$norows_placeholder	 = $form->getAttribute('norows_placeholder', '');

		// Joomla! 3.x sidebar support
		$form_class = '';

		if ($show_filters)
		{
			\JHtmlSidebar::setAction("index.php?option=" .
				$this->container->componentName . "&view=" .
				$this->container->inflector->pluralize($form->getView()->getName())
			);
		}

		// Pre-render the header and filter rows
		$filter_html = '';
		$sortFields	 = array();

		if ($show_header || $show_filters)
		{
			foreach ($headerFields as $headerField)
			{
				$filter		 = $headerField->filter;
				$buttons	 = $headerField->buttons;
				$options	 = $headerField->options;
				$sortable	 = $headerField->sortable;

				// If it's a sortable field, add to the list of sortable fields

				if ($sortable)
				{
					$sortFields[$headerField->name] = \JText::_($headerField->label);
				}


				if (!empty($filter))
				{
					$filter_html .= '<div class="filter-search btn-group pull-left">' . "\n";
					$filter_html .= "\t" . '<label for="title" class="element-invisible">';
					$filter_html .= \JText::_($headerField->label);
					$filter_html .= "</label>\n";
					$filter_html .= "\t$filter\n";
					$filter_html .= "</div>\n";

					if (!empty($buttons))
					{
						$filter_html .= '<div class="btn-group pull-left hidden-phone">' . "\n";
						$filter_html .= "\t$buttons\n";
						$filter_html .= '</div>' . "\n";
					}
				}
				elseif (!empty($options))
				{
					$label = $headerField->label;

					$filterName = $headerField->filterFieldName;
					$filterSource = $headerField->filterSource;

					\JHtmlSidebar::addFilter(
						'- ' . \JText::_($label) . ' -',
						$filterName,
						\JHtml::_(
							'select.options',
							$options,
							'value',
							'text',
							$model->getState($filterSource, ''), true
						)
					);
				}
			}
		}

		// Start the form
		$filter_order		 = $form->getView()->getLists()->order;
		$filter_order_Dir	 = $form->getView()->getLists()->order_Dir;
		$actionUrl           = $this->container->platform->isBackend() ? 'index.php' : \JUri::root().'index.php';

		if ($this->container->platform->isFrontend() && ($this->container->input->getCmd('Itemid', 0) != 0))
		{
			$itemid = $this->container->input->getCmd('Itemid', 0);
			$uri = new \JUri($actionUrl);

			if ($itemid)
			{
				$uri->setVar('Itemid', $itemid);
			}

			$actionUrl = \JRoute::_($uri->toString());
		}

		$html .= '<form action="'.$actionUrl.'" method="post" name="adminForm" id="adminForm" ' . $form_class . '>' . "\n";

		// Get and output the sidebar, if present
		$sidebar = \JHtmlSidebar::render();

		if ($show_filters && !empty($sidebar))
		{
			$html .= '<div id="j-sidebar-container" class="span2">' . "\n";
			$html .= "\t$sidebar\n";
			$html .= "</div>\n";
			$html .= '<div id="j-main-container" class="span10">' . "\n";
		}
		else
		{
			$html .= '<div id="j-main-container">' . "\n";
		}

		// Render header search fields, if the header is enabled
		$pagination = $form->getView()->getPagination();

		if ($show_header)
		{
			$html .= "\t" . '<div id="filter-bar" class="btn-toolbar">' . "\n";
			$html .= "$filter_html\n";

			if ($show_pagination)
			{
				// Render the pagination rows per page selection box, if the pagination is enabled
				$html .= "\t" . '<div class="btn-group pull-right hidden-phone">' . "\n";
				$html .= "\t\t" . '<label for="limit" class="element-invisible">' . \JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC') . '</label>' . "\n";
				$html .= "\t\t" . $pagination->getLimitBox() . "\n";
				$html .= "\t" . '</div>' . "\n";
			}

			if (!empty($sortFields))
			{
				// Display the field sort order
				$asc_sel	 = ($form->getView()->getLists()->order_Dir == 'asc') ? 'selected="selected"' : '';
				$desc_sel	 = ($form->getView()->getLists()->order_Dir == 'desc') ? 'selected="selected"' : '';
				$html .= "\t" . '<div class="btn-group pull-right hidden-phone">' . "\n";
				$html .= "\t\t" . '<label for="directionTable" class="element-invisible">' . \JText::_('JFIELD_ORDERING_DESC') . '</label>' . "\n";
				$html .= "\t\t" . '<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">' . "\n";
				$html .= "\t\t\t" . '<option value="">' . \JText::_('JFIELD_ORDERING_DESC') . '</option>' . "\n";
				$html .= "\t\t\t" . '<option value="asc" ' . $asc_sel . '>' . \JText::_('JGLOBAL_ORDER_ASCENDING') . '</option>' . "\n";
				$html .= "\t\t\t" . '<option value="desc" ' . $desc_sel . '>' . \JText::_('JGLOBAL_ORDER_DESCENDING') . '</option>' . "\n";
				$html .= "\t\t" . '</select>' . "\n";
				$html .= "\t" . '</div>' . "\n\n";

				// Display the sort fields
				$html .= "\t" . '<div class="btn-group pull-right">' . "\n";
				$html .= "\t\t" . '<label for="sortTable" class="element-invisible">' . \JText::_('JGLOBAL_SORT_BY') . '</label>' . "\n";
				$html .= "\t\t" . '<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">' . "\n";
				$html .= "\t\t\t" . '<option value="">' . \JText::_('JGLOBAL_SORT_BY') . '</option>' . "\n";
				$html .= "\t\t\t" . \JHtml::_('select.options', $sortFields, 'value', 'text', $form->getView()->getLists()->order) . "\n";
				$html .= "\t\t" . '</select>' . "\n";
				$html .= "\t" . '</div>' . "\n";
			}

			$html .= "\t</div>\n\n";
			$html .= "\t" . '<div class="clearfix"> </div>' . "\n\n";
		}

		// Start the table output
		$html .= "\t\t" . '<div id="itemsList">' . "\n";

		// Loop through rows and fields, or show placeholder for no rows
		$html .= "\t\t\t<div class=\"row-fluid\">" . "\n";
		$fields		 = $form->getFieldset('items');
		$num_columns = count($fields);
		$items		 = $model->get();

		if ($count = count($items))
		{
			foreach ($items as $i => $item)
			{
				$rowHtml = '';

				$form->bind($item);

				$rowClass   = 'span3';

				$fields = $form->getFieldset('items');

				/** @var FieldInterface $field */
				foreach ($fields as $field)
				{
					$field->rowid	 = $i;
					$field->item	 = $item;
					$labelClass		 = $field->labelclass;
					$class			 = $labelClass ? 'class ="' . $labelClass . '"' : '';

					if (!method_exists($field, 'getRepeatable'))
					{
						throw new \Exception('getRepeatable not found in class ' . get_class($field));
					}

					// Let the fields change the row (tr element) class
					if (method_exists($field, 'getRepeatableRowClass'))
					{
						$rowClass = $field->getRepeatableRowClass($rowClass);
					}

					$rowHtml .= "\t\t\t\t\t<div $class>" . $field->getRepeatable() . '</div>' . "\n";
				}

				$html .= "\t\t\t\t<div class=\"$rowClass\">\n" . $rowHtml . "\t\t\t\t</div>\n";
				$html .= $i % 4 == 0 ? "\t\t\t" . '</div><div class="row-fluid">' : '';
			}
		}
		elseif ($norows_placeholder)
		{
			$html .= "\t\t\t\t<div class=\"row-fluid\"><div class=\"span12\">";
			$html .= \JText::_($norows_placeholder);
			$html .= "</div></div>\n";
		}

		$html .= "\t\t\t</div>" . "\n";

		// End the table output
		$html .= "\t\t" . '</div>' . "\n";

		// Render the pagination bar, if enabled, on J! 3.0+

		$html .= $pagination->getListFooter();

		// Close the wrapper element div on Joomla! 3.0+
		$html .= "</div>\n";

		$html .= "\t" . '<input type="hidden" name="option" value="' . $this->container->componentName . '" />' . "\n";
		$html .= "\t" . '<input type="hidden" name="view" value="' . $this->container->inflector->pluralize($form->getView()->getName()) . '" />' . "\n";
		$html .= "\t" . '<input type="hidden" name="task" value="' . $form->getView()->getTask() . '" />' . "\n";
		$html .= "\t" . '<input type="hidden" name="layout" value="' . $form->getView()->getLayout() . '" />' . "\n";
		$html .= "\t" . '<input type="hidden" name="format" value="' . $this->container->input->getCmd('format', 'html') . '" />' . "\n";

		if ($tmpl = $this->container->input->getCmd('tmpl', ''))
		{
			$html .= "\t" . '<input type="hidden" name="tmpl" value="' . $tmpl . '" />' . "\n";
		}


		// The id field is required in Joomla! 3 front-end to prevent the pagination limit box from screwing it up.
		if ($this->container->platform->isFrontend())
		{
			$html .= "\t" . '<input type="hidden" name="id" value="" />' . "\n";
		}

		$html .= "\t" . '<input type="hidden" name="boxchecked" value="" />' . "\n";
		$html .= "\t" . '<input type="hidden" name="hidemainmenu" value="" />' . "\n";
		$html .= "\t" . '<input type="hidden" name="filter_order" value="' . $filter_order . '" />' . "\n";
		$html .= "\t" . '<input type="hidden" name="filter_order_Dir" value="' . $filter_order_Dir . '" />' . "\n";

		$html .= "\t" . '<input type="hidden" name="' . \JFactory::getSession()->getFormToken() . '" value="1" />' . "\n";

		// End the form
		$html .= '</form>' . "\n";

		return $html;
	}
}
