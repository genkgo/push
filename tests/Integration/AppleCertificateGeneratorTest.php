<?php
namespace Genkgo\Push\Integration;

use Genkgo\Push\AbstractTestCase;
use Genkgo\Push\Certificate\Apple\Generator;
use Genkgo\Push\Certificate\Apple\PortalConnection;
use Genkgo\Push\Certificate\Apple\PushCertificate;
use Genkgo\Push\Certificate\Apple\Type;
use Genkgo\Push\Exception\ApplePortalException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class AppleCertificateGeneratorTest extends AbstractTestCase
{
    public function testGenerate()
    {
        $appleId = 'test@apple.com';
        $password = 'password';
        $teamId = 'team';
        $appIdKey = '891bd3417a7776362562d2197f89480a8547b108fd934911bcbea0110d07f757';
        $redirectUrl = 'https://idmsa.apple.com/IDMSWebAuth/login?&appIdKey=' . $appIdKey . '&path=%2F%2Fmembercenter%2Findex.action';

        $client = $this->getMock(Client::class);
        $client->expects($this->at(0))
            ->method('__call')
            ->with('get', $this->callback(function ($arguments) {
                $this->assertEquals($arguments[0], 'https://developer.apple.com/account/');
                return $arguments;
            }))
            ->willReturn(
                new Response(200, ['Location' => $redirectUrl])
            )
        ;

        $client->expects($this->at(1))
            ->method('__call')
            ->with('post', $this->callback(function ($arguments) use ($appleId, $password, $appIdKey) {
                $this->assertEquals($arguments[0], 'https://idmsa.apple.com/IDMSWebAuth/authenticate');
                $this->assertEquals($arguments[1]['form_params']['appleId'], $appleId);
                $this->assertEquals($arguments[1]['form_params']['accountPassword'], $password);
                $this->assertEquals($arguments[1]['form_params']['appIdKey'], $appIdKey);
                return $arguments;
            }))
            ->willReturn(
                new Response(200, [], 'correct')
            )
        ;

        $client->expects($this->at(2))
            ->method('__call')
            ->with('post', $this->callback(function ($arguments) use ($teamId, $appIdKey) {
                $this->assertEquals($arguments[0], 'https://developer.apple.com/services-account/QH65B2/account/ios/identifiers/listAppIds.action');
                $this->assertEquals($arguments[1]['form_params']['teamId'], $teamId);
                return $arguments;
            }))
            ->willReturn(
                new Response(200, ['csrf' => 'csrf1', 'csrf_ts' => 'csrf2'], 'correct')
            )
        ;

        $apps = [
            'appIds' => [[
                'identifier' => 'app1',
                'appIdId' => 'A1',
                'name' => 'First App',
            ]]
        ];

        $client->expects($this->at(3))
            ->method('__call')
            ->with('post', $this->callback(function ($arguments) use ($teamId, $appIdKey) {
                $this->assertEquals($arguments[0], 'https://developer.apple.com/services-account/QH65B2/account/ios/identifiers/listAppIds.action');
                $this->assertEquals($arguments[1]['form_params']['teamId'], $teamId);
                return $arguments;
            }))
            ->willReturn(
                new Response(200, [], json_encode($apps))
            )
        ;

        $certificate = [
            'certRequest' => [
                'certificate' => [
                    'certificateId' => 'C1'
                ]
            ]
        ];

        $client->expects($this->at(4))
            ->method('__call')
            ->with('post', $this->callback(function ($arguments) use ($teamId, $appIdKey) {
                $this->assertEquals($arguments[0], 'https://developer.apple.com/services-account/QH65B2/account/ios/certificate/submitCertificateRequest.action');
                $this->assertEquals($arguments[1]['form_params']['teamId'], $teamId);
                $this->assertEquals($arguments[1]['form_params']['type'], Type::DEVELOPMENT);
                $this->assertEquals($arguments[1]['form_params']['appIdId'], 'A1');
                $this->assertEquals($arguments[1]['headers']['csrf'], 'csrf1');
                $this->assertEquals($arguments[1]['headers']['csrf_ts'], 'csrf2');
                $this->assertStringStartsWith('-----BEGIN CERTIFICATE REQUEST-----', $arguments[1]['form_params']['csrContent']);
                return $arguments;
            }))
            ->willReturn(
                new Response(200, [], json_encode($certificate))
            )
        ;

        $client->expects($this->at(5))
            ->method('__call')
            ->with('get', $this->callback(function ($arguments) use ($teamId, $appIdKey) {
                $this->assertEquals($arguments[0], 'https://developer.apple.com/services-account/QH65B2/account/ios/certificate/downloadCertificateContent.action');
                $this->assertEquals($arguments[1]['query']['teamId'], $teamId);
                $this->assertEquals($arguments[1]['query']['type'], Type::DEVELOPMENT);
                $this->assertEquals($arguments[1]['query']['certificateId'], 'C1');
                $this->assertEquals($arguments[1]['headers']['csrf'], 'csrf1');
                $this->assertEquals($arguments[1]['headers']['csrf_ts'], 'csrf2');
                return $arguments;
            }))
            ->willReturn(
                new Response(200, [], file_get_contents(__DIR__ . '/../Stubs/signed.certificate.cer'))
            )
        ;

        $generator = new Generator(new PortalConnection($client, $appleId, $password, $teamId));
        $pushCertificate = $generator->generate(new Type(Type::DEVELOPMENT), 'app1');
        $this->assertInstanceOf(PushCertificate::class, $pushCertificate);
    }

    public function testInvalidLogin()
    {
        $this->setExpectedException(ApplePortalException::class);
        $appleId = 'test@apple.com';
        $password = 'password';
        $teamId = 'team';
        $appIdKey = '891bd3417a7776362562d2197f89480a8547b108fd934911bcbea0110d07f757';
        $redirectUrl = 'https://idmsa.apple.com/IDMSWebAuth/login?&appIdKey=' . $appIdKey . '&path=%2F%2Fmembercenter%2Findex.action';

        $client = $this->getMock(Client::class);
        $client->expects($this->at(0))
            ->method('__call')
            ->with('get', $this->callback(function ($arguments) {
                $this->assertEquals($arguments[0], 'https://developer.apple.com/account/');
                return $arguments;
            }))
            ->willReturn(
                new Response(200, ['Location' => $redirectUrl])
            )
        ;

        $client->expects($this->at(1))
            ->method('__call')
            ->with('post', $this->callback(function ($arguments) use ($appleId, $password, $appIdKey) {
                $this->assertEquals($arguments[0], 'https://idmsa.apple.com/IDMSWebAuth/authenticate');
                $this->assertEquals($arguments[1]['form_params']['appleId'], $appleId);
                $this->assertEquals($arguments[1]['form_params']['accountPassword'], $password);
                $this->assertEquals($arguments[1]['form_params']['appIdKey'], $appIdKey);
                return $arguments;
            }))
            ->willReturn(
                new Response(200, [], 'entered incorrectly')
            )
        ;

        $generator = new Generator(new PortalConnection($client, $appleId, $password, $teamId));
        $generator->generate(new Type(Type::DEVELOPMENT), 'app1');
    }

    public function testUnknownApp()
    {
        $this->setExpectedException(ApplePortalException::class);
        $appleId = 'test@apple.com';
        $password = 'password';
        $teamId = 'team';
        $appIdKey = '891bd3417a7776362562d2197f89480a8547b108fd934911bcbea0110d07f757';
        $redirectUrl = 'https://idmsa.apple.com/IDMSWebAuth/login?&appIdKey=' . $appIdKey . '&path=%2F%2Fmembercenter%2Findex.action';

        $client = $this->getMock(Client::class);
        $client->expects($this->at(0))
            ->method('__call')
            ->with('get', $this->callback(function ($arguments) {
                $this->assertEquals($arguments[0], 'https://developer.apple.com/account/');
                return $arguments;
            }))
            ->willReturn(
                new Response(200, ['Location' => $redirectUrl])
            )
        ;

        $client->expects($this->at(1))
            ->method('__call')
            ->with('post', $this->callback(function ($arguments) use ($appleId, $password, $appIdKey) {
                $this->assertEquals($arguments[0], 'https://idmsa.apple.com/IDMSWebAuth/authenticate');
                $this->assertEquals($arguments[1]['form_params']['appleId'], $appleId);
                $this->assertEquals($arguments[1]['form_params']['accountPassword'], $password);
                $this->assertEquals($arguments[1]['form_params']['appIdKey'], $appIdKey);
                return $arguments;
            }))
            ->willReturn(
                new Response(200, [], 'correct')
            )
        ;

        $client->expects($this->at(2))
            ->method('__call')
            ->with('post', $this->callback(function ($arguments) use ($teamId, $appIdKey) {
                $this->assertEquals($arguments[0], 'https://developer.apple.com/services-account/QH65B2/account/ios/identifiers/listAppIds.action');
                $this->assertEquals($arguments[1]['form_params']['teamId'], $teamId);
                return $arguments;
            }))
            ->willReturn(
                new Response(200, ['csrf' => 'csrf1', 'csrf_ts' => 'csrf2'], 'correct')
            )
        ;

        $apps = [
            'appIds' => [[
                'identifier' => 'app2',
                'appIdId' => 'A2',
                'name' => 'Second App',
            ]]
        ];

        $client->expects($this->at(3))
            ->method('__call')
            ->with('post', $this->callback(function ($arguments) use ($teamId, $appIdKey) {
                $this->assertEquals($arguments[0], 'https://developer.apple.com/services-account/QH65B2/account/ios/identifiers/listAppIds.action');
                $this->assertEquals($arguments[1]['form_params']['teamId'], $teamId);
                return $arguments;
            }))
            ->willReturn(
                new Response(200, [], json_encode($apps))
            )
        ;

        $generator = new Generator(new PortalConnection($client, $appleId, $password, $teamId));
        $generator->generate(new Type(Type::DEVELOPMENT), 'app1');
    }
}
