<?php
/**
 * File containing the ezpDatabaseTestCase class
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package tests
 */

/**
 * Database backed test case class.
 *
 * Inherit from this class if you want your test case to interact with a database.
 */
class ezpDatabaseTestCase extends ezpTestCase
{
    /**
     * Holds paths to custom sql files
     *
     * @var array( array( string => string ) )
     */
    protected $sqlFiles = array();

    /**
     * Controls if the database should be initialized with default data
     *
     * @var bool
     */
    protected $insertDefaultData = true;

    /**
     * Hold shared fixtures
     * 
     * @var mixed
     */
    protected $sharedFixture;

    /**
     * Sets up the database environment
     */
    protected function setUp()
    {
        parent::setUp();
        if ( ezpTestRunner::dbPerTest() )
        {
            $dsn = ezpTestRunner::dsn();
            $this->sharedFixture = ezpTestDatabaseHelper::create( $dsn );

            if ( $this->insertDefaultData === true )
                ezpTestDatabaseHelper::insertDefaultData( $this->sharedFixture );

            if ( count( $this->sqlFiles > 0 ) )
                ezpTestDatabaseHelper::insertSqlData( $this->sharedFixture, $this->sqlFiles );

            eZDB::setInstance( $this->sharedFixture );
        }
    }

    protected function tearDown()
    {
        if ( ezpTestRunner::dbPerTest() )
        {
            $db = eZDB::instance();
            $db->close();
        }
        parent::tearDown();
    }
}

?>
