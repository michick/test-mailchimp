<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Submit', $crawler->text());
    }

    /**
     * @dataProvider getValidEmails
     */
    public function testSubscribe($email)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/', [
            'email' => $email
        ]);

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertContains('Congratulations', $crawler->text());
    }

    /**
     * @dataProvider getInvalidEmails
     */
    public function testSubscribeWrongEmail($email)
    {
        $client = static::createClient();
        $randEmail = $email;

        $crawler = $client->request('GET', '/', [
            'email' => $randEmail
        ]);

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertContains('The provided email is not valid', $crawler->text());
    }

    /**
     * @return array
     */
    public function getValidEmails()
    {
        $res = [[]];

        for ($i = 0; $i < 5; $i++) {
            $res[0][] = self::getRandString() . '@gmail.com';
        }

        return $res;
    }

    /**
     * @return array
     */
    public function getInvalidEmails()
    {
        return [
            ['assafdsadf'],
            ['notgood@'],
            ['notgood@some'],
        ];
    }

    /**
     * @param int $length
     * @return string
     */
    protected static function getRandString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);

        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
