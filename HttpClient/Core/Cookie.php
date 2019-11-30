<?php


namespace HttpClient\Core;


class Cookie
{
    private
        $_name,
        $_value,
        $_expires,
        $_domain,
        $_path;

    private $_cookieLine;

    public function __construct(string $setCookieLine)
    {
        if (!$setCookieLine)
            return;

        $this->_cookieLine = $setCookieLine;

        $this->assignNameValue($setCookieLine, $exploded);

        $this->assignCookieProperties($exploded);

    }

    public function __toString(): string
    {
        return $this->_cookieLine;
    }

    public function __get($name)
    {
        $key = "_{$name}";
        if (isset($this->{$key}))
            return $this->{$key};
        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @return string
     */
    public function getExpires()
    {
        return $this->_expires;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->_domain;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    private function assignNameValue(string $setCookieLine, &$exploded): void
    {
        $exploded = explode(';', $setCookieLine);

        list($cookieName, $cookieValue) = explode('=', $exploded[0]);
        $this->_name = $cookieName;
        $this->_value = $cookieValue;
        unset($exploded[0]);
    }

    private function assignCookieProperties($exploded): void
    {
        foreach ($exploded as $item) {

            $property = trim(strtolower($item));

            if ($property === 'httponly')
            {
                $this->httpOnly = true;
                continue;
            }

            list($key, $value) = explode('=', $property);

            if ($value)
                $this->{'_' . $key} = $value;
        }
    }


}