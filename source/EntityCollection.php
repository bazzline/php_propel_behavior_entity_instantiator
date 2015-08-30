<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-08-29
 */
namespace Net\Bazzline\Propel\Behavior\EntityInstantiator;

use ArrayAccess;
use Countable;
use Iterator;
use InvalidArgumentException;

class EntityCollection implements ArrayAccess, Countable, Iterator
{
    /** @var array */
    private $entityCollection;

    /** @var array */
    private $hashCollection;

    public function __construct()
    {
        $this->entityCollection = array();
        $this->hashCollection   = array();
    }

    /**
     * @param AbstractEntity $entity
     * @throws InvalidArgumentException
     */
    public function add(AbstractEntity $entity)
    {
        $this->throwInvalidArgumentExceptionIfEntityWasAlreadyAdded($entity);
        $this->entityCollection[] = $entity;
    }

    /**
     * @param AbstractEntity $entity
     * @throws InvalidArgumentException
     */
    private function throwInvalidArgumentExceptionIfEntityWasAlreadyAdded(AbstractEntity $entity)
    {
        $hash = sha1($entity->databaseName() . '_' . $entity->methodName());

        if (isset($this->hashCollection[$hash])) {
            throw new InvalidArgumentException(
                'you are trying to add "' . $entity->methodName() .
                '" twice for the database "' . $entity->databaseName() . '"'
            );
        }

        $this->hashCollection[$hash] = $entity;
    }

    //begin of ArrayAccess
    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->entityCollection[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return ($this->offsetExists($offset))
            ? $this->entityCollection[$offset]
            : null;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->entityCollection[] = $value;
        } else {
            $this->entityCollection[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->entityCollection[$offset]);
    }

    //end of ArrayAccess

    //begin of Countable
    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->entityCollection);
    }
    //end of Countable

    //begin of Iterator
    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->entityCollection);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        next($this->entityCollection);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->entityCollection);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return (!is_null($this->key()));
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->entityCollection);
    }
    //end of Iterator
}