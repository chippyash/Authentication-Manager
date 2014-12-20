<?php
/**
 * Chippyash Digest Authentication Manager
 * 
 * @copyright Ashley Kitson, UK, 2014
 * @license GPL 3.0+
 */
namespace chippyash\Authentication\Manager\Digest;

use chippyash\Authentication\Manager\Digest\DigestCollectionInterface;
use chippyash\Authentication\Manager\Encoder\DigestEncoderInterface;
use chippyash\Authentication\Manager\Exceptions\AuthManagerException;
use chippyash\Type\String\StringType;
use chippyash\Type\Number\IntType;
use chippyash\Type\BoolType;

/**
 * A collection of Digests
 */
abstract class AbstractDigestCollection implements DigestCollectionInterface, \Countable
{
    const ERR_NO_DIGEST_TPL = 'No digest at index %d';
    
    /**
     * File write options
     * @see \file_put_contents
     * 
     * @var int
     */
    protected $writeOptions = LOCK_EX;
    
    /**
     * Name of file that digest collection is stored in
     * 
     * @var StringType
     */
    protected $fileName;
    
    /**
     * Digest encoder
     * @var chippyash\Authentication\Manager\Encoder\DigestEncoderInterface
     */
    protected $encoder;
    
    /**
     * Collection of digest items
     * 
     * @var array
     */
    protected $collection = [];
    
    public function __construct(StringType $fileName, array $digests = [])
    {
        $this->collection = $digests;
        $this->fileName = $fileName;
    }
    
    /**
     * Set file writing options
     * @see \file_put_contents
     * 
     * @param IntType $options file_put_contents options
     * 
     * @return Fluent Interface
     */
    public function setWriteOptions(IntType $options){
        $this->writeOptions = $options();
        return $this;        
    }   
    
    /**
     * Set the encoder
     * 
     * @param DigestEncoderInterface $encoder
     * @return Fluent Interface
     */
    public function setEncoder(DigestEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        
        return $this;
    }
    
    /**
     * @interface \Countable
     * 
     * @return int
     */
    public function count()
    {
        return count($this->collection);
    }
    
    /**
     * Get digest item
     * 
     * @param IntType $index
     * 
     * @return array Digest item
     * 
     * @throws chippyash\Authentication\Manager\Exceptions\AuthManagerException
     */
    public function get(IntType $index)
    {
        if (!isset($this->collection[$index()])) {
            throw new AuthManagerException(sprintf(self::ERR_NO_DIGEST_TPL, $index()));
        }
        
        return $this->collection[$index()];
    }
    
    /**
     * Delete digest item
     * 
     * @param IntType $index
     * 
     * @return chippyash\Type\BoolType true on success else false
     */
    public function del(IntType $index)
    {
        if (!isset($this->collection[$index()])) {
            return new BoolType(false);
        }
        
        unset($this->collection[$index()]);
        $this->collection = array_values($this->collection);
        
        return new BoolType(true);
    }

    /**
     * Return index into collection for a digest given its uid
     * 
     * @param StringType $uid user id
     * 
     * @return chippyash\Type\Number\IntType|false
     */
    abstract public function findByUid(StringType $uid);
    
    /**
     * Read the digest into the collection from file
     * 
     * @return chippyash\Type\BoolType true on success else false
     */
    abstract public function read();
    
    /**
     * Write the collection to file
     * 
     * @return chippyash\Type\BoolType true on success else false
     */
    abstract public function write();
    
    /**
     * Add a digest line to the collection
     * 
     * @param StringType $uid user id
     * @param StringType $pwd password
     * 
     * @return chippyash\Type\BoolType true on success else false
     */
    abstract public function add(StringType $uid, StringType $pwd);
    
    /**
     * Return the collection item as a raw digest string
     * 
     * @param IntType $index Index into collection
     * 
     * @return StringType
     */
    abstract public function asString(IntType $index);
}
