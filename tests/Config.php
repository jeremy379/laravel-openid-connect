<?php

namespace OpenIDConnect\Tests;

class Config
{
    /**
     * The $_SERVER['HTTP_HOST'] variable is not set form CLI, so we add it
     */
    public const HTTP_HOST = 'example.com';

    /**
     * The $_SERVER['REQUEST_SCHEME'] variable is not set form CLI, so we add it
     */
    public const REQUEST_SCHEME = 'https';

    /**
     * An ID for the third party client
     */
    public const CLIENT_ID = '1';

    /**
     * The ID of the user to test with
     */
    public const USER_ID = '1';
}
