<?php
namespace EllisLab\ExpressionEngine\Model\Query;

use EllisLab\ExpressionEngine\Library\DataStructure\Tree\TreeNode;

class QueryTreeNode extends TreeNode {

	public static $top_id = 0;

	protected $id = 0;

	protected $children_by_id = array();
	protected $path_string = NULL;

	public function __construct($name)
	{
		parent::__construct($name, array());
		$this->id = ++self::$top_id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getChildById($id)
	{
		return $this->children_by_id[$id];
	}

	public function add(TreeNode $child)
	{
		if ( ! ($child instanceof QueryTreeNode))
		{
			throw new InvalidArgumentException('QueryTreeNodes can only be used with other QueryTreeNodes.');
		}

		$this->children_by_id[$child->getId()] = $child;
		parent::add($child);
	}

	/**
	 * Create a string representing the path from the root node
	 * to this node using the unique ids of each node along the
	 * path.
	 */
	public function getPathString()
	{
		if ( ! isset($this->path_string))
		{
			$node = $this;
			$path = $this->getId();

			while ( ! $node->isRoot())
			{
				$path = $node->getParent()->getId() . '_' . $path;
				$node = $node->getParent();
			}

			$this->path_string = $path;
		}

		return $this->path_string;
	}
}
