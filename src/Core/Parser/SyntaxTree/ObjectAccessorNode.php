<?php
namespace TYPO3\Fluid\Core\Parser\SyntaxTree;

/*
 * This file belongs to the package "TYPO3 Fluid".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\Fluid\Core\Variables\VariableExtractor;

/**
 * A node which handles object access. This means it handles structures like {object.accessor.bla}
 */
class ObjectAccessorNode extends AbstractNode {

	/**
	 * Object path which will be called. Is a list like "post.name.email"
	 *
	 * @var string
	 */
	protected $objectPath;

	/**
	 * Accessor names, one per segment in the object path. Use constants from VariableExtractor
	 *
	 * @var array
	 */
	protected $accessors = array();

	/**
	 * @var array
	 */
	protected static $variables = array();

	/**
	 * Constructor. Takes an object path as input.
	 *
	 * The first part of the object path has to be a variable in the
	 * VariableProvider.
	 *
	 * @param string $objectPath An Object Path, like object1.object2.object3
	 */
	public function __construct($objectPath, array $accessors = array()) {
		$this->objectPath = $objectPath;
		$this->accessors = $accessors;
	}


	/**
	 * Internally used for building up cached templates; do not use directly!
	 *
	 * @return string
	 */
	public function getObjectPath() {
		return $this->objectPath;
	}

	/**
	 * @return array
	 */
	public function getAccessors() {
		return $this->accessors;
	}

	/**
	 * Evaluate this node and return the correct object.
	 *
	 * Handles each part (denoted by .) in $this->objectPath in the following order:
	 * - call appropriate getter
	 * - call public property, if exists
	 * - fail
	 *
	 * The first part of the object path has to be a variable in the
	 * VariableProvider.
	 *
	 * @param RenderingContextInterface $renderingContext
	 * @return object The evaluated object, can be any object type.
	 */
	public function evaluate(RenderingContextInterface $renderingContext) {
		$variableProvider = $renderingContext->getVariableProvider();
		switch (strtolower($this->objectPath)) {
			case '_all':
				return $variableProvider;
			case 'true':
			case 'on':
			case 'yes':
				return TRUE;
			case 'false':
			case 'off':
			case 'no':
				return FALSE;
			default:
				return VariableExtractor::extract($variableProvider, $this->objectPath, $this->accessors);
		}
	}

}
