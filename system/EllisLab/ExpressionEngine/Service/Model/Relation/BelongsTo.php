<?php
namespace EllisLab\ExpressionEngine\Service\Model\Relation;

use EllisLab\ExpressionEngine\Service\Model\Model;
use EllisLab\ExpressionEngine\Service\Model\Association;

class BelongsTo extends Relation {

	/**
	 *
	 */
	public function createAssociation(Model $source)
	{
		return new Association\BelongsTo($source, $this->name);
	}

	/**
	 *
	 */
	public function linkIds(Model $source, Model $target)
	{
		list($from, $to) = $this->getKeys();

		$source->$from = $source->$to;
	}

	/**
	 *
	 */
	public function unlinkIds(Model $source, Model $target)
	{
		list($from, $_) = $this->getKeys();

		$source->$from = NULL;
	}

	/**
	 *
	 */
	protected function deriveKeys()
	{
		$to   = $this->to_key   ?: $this->to_primary_key;
		$from = $this->from_key ?: $to;

		return array($from, $to);
	}
}