<?php
/**
 * @package     Jobs
 * @subpackage  com_jobs
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * View class for a list of jobs.
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
class JobsViewJobs extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   3.0
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->state      = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->items      = $this->get('Items');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Load the submenu.
		JobsHelper::addSubmenu('jobs');

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function addToolbar()
	{
		// Include dependancies.
		require_once JPATH_COMPONENT . '/helpers/jobs.php';

		// Initialise variables.
		$state = $this->get('State');
		$canDo = JobsHelper::getActions();
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_JOBS_MANAGER_JOBS'), 'jobs.png');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('job.add');
		}

		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('job.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('jobs.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('jobs.unpublish', 'JTOOLBAR_UNPUBLISH', true);

			JToolbarHelper::archiveList('jobs.archive');
			JToolbarHelper::checkin('jobs.checkin');
		}

		if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'jobs.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('jobs.trash');
		}

		// Add a batch button
		if ($canDo->get('core.edit'))
		{
			JHtml::_('bootstrap.modal', 'collapseModal');
			$title = JText::_('JTOOLBAR_BATCH');
			$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
						<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
						$title</button>";
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_jobs');
		}

		JToolBarHelper::help('jobs', $com = true);

		JHtmlSidebar::setAction('index.php?option=com_jobs&view=jobs');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.state' => JText::_('JSTATUS'),
			'c.name' => JText::_('COM_JOBS_HEADING_COMPANY'),
			'a.title' => JText::_('JGLOBAL_TITLE'),
			'a.number' => JText::_('COM_JOBS_HEADING_JOB_OFFERS'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.hits' => JText::_('JGLOBAL_HITS'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
