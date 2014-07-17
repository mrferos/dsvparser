<?php

class DSVParser implements Iterator
{
    /**
     * @var string
     */
    protected $_delimiter = ',';
    /**
     * @var string
     */
    protected $_enclosure = '"';
    /**
     * @var stream
     */
    protected $_escape = "\\";
    /**
     * @var resource
     */
    protected $_fileStream;
    /**
     * @var array
     */
    protected $_columnHeaders = array();
    /**
     * @var boolean
     */
    protected $_firstRowAsColumnHeaders;
    /**
     * @var string
     */
    protected $_fileName;
    protected $_position;
    protected $_positionIndex = -1;
    protected $_current = array();

    public function open()
    {
        $fileStream = $this->getFileStream();
        if (is_resource($fileStream)) {
            throw new RuntimeException('Filestream already open, close current one');
        }

        $fileName = $this->getFileName();
        if (!empty($fileName)) {
            $this->setFileStream(fopen($fileName, 'r'));
            return true;
        }

        throw new RuntimeException('Could not open filestream');
    }

    public function close()
    {
        $fileStream = $this->getFileStream();
        if (!is_resource($fileStream)) {
            throw new RuntimeException('No open filestream');
        }

        fclose($fileStream);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->_current;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $data = fgetcsv($this->getFileStream(), 0, $this->getDelimiter(), $this->getEnclosure(), $this->getEscape());
        $columnHeaders = $this->getColumnHeaders();
        if (!empty($columnHeaders)) {
            $data = array_combine($columnHeaders, $data);
        }

        $this->_positionIndex++;
        $this->_current = $data;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->_positionIndex;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return !feof($this->getFileStream());
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        rewind($this->getFileStream());
        $this->_positionIndex = 0;
        $firstRowAsColumnHeaders = $this->getFirstRowAsColumnHeaders();
        if ($firstRowAsColumnHeaders) {
            $this->next();
            $this->setColumnHeaders($this->current());
            $this->next();
            $this->_positionIndex = 0;
        }
    }


    /**
     * @param array $columnHeaders
     */
    public function setColumnHeaders($columnHeaders)
    {
        $this->_columnHeaders = $columnHeaders;
    }

    /**
     * @return array
     */
    public function getColumnHeaders()
    {
        return $this->_columnHeaders;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->_delimiter = $delimiter;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->_delimiter;
    }

    /**
     * @param string $enclosure
     */
    public function setEnclosure($enclosure)
    {
        $this->_enclosure = $enclosure;
    }

    /**
     * @return string
     */
    public function getEnclosure()
    {
        return $this->_enclosure;
    }

    /**
     * @param \stream $escape
     */
    public function setEscape($escape)
    {
        $this->_escape = $escape;
    }

    /**
     * @return \stream
     */
    public function getEscape()
    {
        return $this->_escape;
    }

    /**
     * @param string $fileName
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function setFileName($fileName)
    {
        if (is_resource($this->_fileStream)) {
            throw new RuntimeException('Open filestream, call close() first');
        }

        if (!file_exists($fileName)) {
            throw new InvalidArgumentException('Supplied file does not exist');
        }

        $this->_fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->_fileName;
    }

    /**
     * @param resource $fileStream
     */
    public function setFileStream($fileStream)
    {
        $this->_fileStream = $fileStream;
    }

    /**
     * @return resource
     * @throws RuntimeException
     */
    public function getFileStream()
    {
        return $this->_fileStream;
    }

    /**
     * @param boolean $firstRowAsColumnHeaders
     * @throws InvalidArgumentException
     */
    public function setFirstRowAsColumnHeaders($firstRowAsColumnHeaders)
    {
        if (!is_bool($firstRowAsColumnHeaders)) {
            throw new InvalidArgumentException('$firstRowAsColumnHeaders must be a boolean');
        }

        $this->_firstRowAsColumnHeaders = $firstRowAsColumnHeaders;
    }

    /**
     * @return boolean
     */
    public function getFirstRowAsColumnHeaders()
    {
        return $this->_firstRowAsColumnHeaders;
    }
}
