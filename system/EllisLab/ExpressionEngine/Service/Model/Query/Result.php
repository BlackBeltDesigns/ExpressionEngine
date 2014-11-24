<?php
namespace EllisLab\ExpressionEngine\Service\Model\Query;

use EllisLab\ExpressionEngine\Service\Model\Collection;

class Result {

	protected $builder;
	protected $frontend;

	protected $db_result;

	protected $columns = array();
	protected $aliases = array();
	protected $objects = array();
	protected $relations = array();

	protected $related_ids = array();

	public function __construct(Builder $builder, $db_result, $aliases, $relations)
	{
		$this->builder = $builder;
		$this->db_result = $db_result;
		$this->aliases = $aliases;
		$this->relations = array_reverse($relations);
	}

	public function first()
	{
		$this->collectColumnsByAliasPrefix($this->db_result[0]);
		$this->initializeResultArray();
		$this->parseRow($row);

		$root = $this->builder->getFrom();
		return $this->objects[$root][0];
	}

	public function all()
	{
		if ( ! count($this->db_result))
		{
			return NULL;
		}

		$this->collectColumnsByAliasPrefix($this->db_result[0]);
		$this->initializeResultArray();

		foreach ($this->db_result as $row)
		{
			$this->parseRow($row);
		}

		$this->constructRelationshipTree();

		reset($this->aliases);
		$root = key($this->aliases);
		return new Collection($this->objects[$root]);
	}

	/**
	 *
	 */
	protected function parseRow($row)
	{
		$by_row = array();

		foreach ($this->columns as $alias => $columns)
		{
			$model_data = array();

			foreach ($columns as $property)
			{
				$value = $row["{$alias}__{$property}"];

				if (isset($value))
				{
					$model_data[$property] = $value;
				}
			}

			if (empty($model_data))
			{
				continue;
			}

			$name = $this->aliases[$alias];

			$object = $this->frontend->make($name);
			$object->fill($model_data);

			$this->objects[$alias][$object->getId()] = $object;

			$by_row[$alias] = $object->getId();
		}

		foreach ($by_row as $alias => $id)
		{
			$related = $by_row;
			unset($related[$alias]);

			if ( ! isset($this->related_ids[$alias]))
			{
				$this->related_ids[$alias] = array();
			}

			if ( ! isset($this->related_ids[$alias][$id]))
			{
				$this->related_ids[$alias][$id] = array();
			}

			$this->related_ids[$alias][$id][] = $related;
		}
	}

	/**
	 *
	 */
	protected function constructRelationshipTree()
	{
		foreach ($this->relations as $to_alias => $lookup)
		{
			$kids = $this->objects[$to_alias];

			foreach ($lookup as $from_alias => $relation)
			{
				$parents = $this->objects[$from_alias];

				$related_ids = $this->matchIds($parents, $from_alias, $to_alias);

				$this->matchRelation($parents, $kids, $related_ids, $relation);
			}
		}
	}

	/**
	 *
	 */
	protected function matchIds($parents, $from_alias, $to_alias)
	{
		$related_ids = array();

		foreach ($parents as $p_id => $parent)
		{
			$related_ids[$p_id] = array();

			$all_related = $this->related_ids[$from_alias][$p_id];

			foreach ($all_related as $potential)
			{
				if (isset($potential[$to_alias]))
				{
					$related_ids[$p_id][] = $potential[$to_alias];
				}
			}
		}

		return $related_ids;
	}

	/**
	 *
	 */
	protected function matchRelation($parents, $kids, $related_ids, $relation)
	{
		foreach ($parents as $p_id => $parent)
		{
			$set = array_unique($related_ids[$p_id]);
			$collection = array();

			foreach ($set as $id)
			{
				$collection[] = $kids[$id];
			}

			$name = $relation->getName();
			$parent->{'fill'.$name}($collection);
		}
	}

	/**
	 * Group all columns by their alias prefix.
	 */
	protected function collectColumnsByAliasPrefix($row)
	{
		$columns = array();

		foreach (array_keys($row) as $column)
		{
			list($alias, $property) = explode('__', $column);

			if ( ! array_key_exists($alias, $columns))
			{
				$columns[$alias] = array();
			}

			$columns[$alias][] = $property;
		}

		$this->columns = $columns;
	}

	/**
	 * Set up an array to hold all of our temporary data.
	 */
	protected function initializeResultArray()
	{
		foreach ($this->aliases as $alias => $model)
		{
			$this->objects[$alias] = array();
		}
	}

	public function setFrontend($frontend)
	{
		$this->frontend = $frontend;
		return $this;
	}
}