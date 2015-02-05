<?php

namespace EllisLab\ExpressionEngine\Model\Content\Display;

class LayoutDisplay {

	protected $tabs = array();

	public function addTab(LayoutTab $tab)
	{
		$this->tabs[$tab->id] = $tab;
	}

	public function setTabs(array $tabs)
	{
		foreach ($tabs as $tab)
		{
			$this->addTab($tab);
		}
	}

	public function getTab($tab_id)
	{
		if ( ! array_key_exists($tab_id, $this->tabs))
		{
			throw new InvalidArgumentException("No such tab: '{$tab_id}' on ".get_called_class());
		}

		return $this->tabs[$tab_id];
	}

	public function getTabs()
	{
		return array_values($this->tabs);
	}

	public function getFields()
	{
		$fields = array();

		foreach ($this->getTabs() as $tab)
		{
			$fields = array_merge($fields, $tab->getFields());
		}

		return $fields;
	}
}