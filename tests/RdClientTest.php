<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use RD\RDStationClient;

class RdClientTest extends TestCase
{
    private $rd_client;

    public function setUp()
    {
        parent::setUp();

        $this->rd_client = new RDStationClient(
            getenv("TOKEN_PUBLIC"),
            getenv("TOKEN_PRIVATE")
        );
    }

    /** @test **/
    public function createNewLead()
    {
        $email = "renan@raessolucoes.com.br";

        $result = $this->rd_client->createNewLead($email);
        $this->assertTrue($result);
    }
}
