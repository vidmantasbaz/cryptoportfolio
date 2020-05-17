<?php


namespace App\Tests\Controller;


use App\Controller\AssetController;
use App\Entity\Asset;
use App\Entity\User;
use App\Exception\AssetException;
use App\Exception\FormValidationException;
use App\Repository\AssetRepository;
use App\Service\Exceptions\ValidProviderNotFound;
use App\Service\ExchangeService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AssetControllerTest extends TestCase
{
    /**
     * @var MockObject|FormInterface
     */
    private $form;
    /**
     * @var MockObject|ExchangeService
     */
    private $service;
    /**
     * @var MockObject|EntityManagerInterface
     */
    private $em;
    /**
     * @var MockObject|ContainerInterface
     */
    private $container;
    /**
     * @var MockObject|TokenInterface
     */
    private $token;
    /**
     * @var MockObject|FormFactory
     */
    private $formFactory;

    /**
     * @var MockObject|TokenStorageInterface
     */
    private $storage;
    /**
     * @var MockObject|AssetRepository
     */
    private $repo;
    /**
     * @var MockObject|AssetController
     */
    private $controller;

    protected function setUp(): void
    {
        $this->form = $this->createMock(FormInterface::class);
        $this->service = $this->getMockBuilder(ExchangeService::class)->disableOriginalConstructor()->getMock();
        $this->em = $this->createMock(EntityManager::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->formFactory = $this->createMock(FormFactory::class);
        $this->storage = $this->createMock(TokenStorageInterface::class);
        $this->repo = $this->createMock(AssetRepository::class);
        $this->controller = new AssetController($this->service, $this->em);
        $this->controller->setContainer($this->container);
    }

    /** @test */
    public function createTest()
    {

        $this->form->expects($this->once())->method('isValid')->willReturn(true);
        $this->form->expects($this->once())->method('isSubmitted')->willReturn(true);

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('getUsername')->willReturn('test');

        $this->token->expects($this->once())->method('getUser')->willReturn($user);
        $this->storage->expects($this->once())->method('getToken')->willReturn($this->token);
        $this->formFactory->expects($this->once())->method('create')->willReturn($this->form);

        $this->container
            ->expects($this->at(0))
            ->method('get')
            ->with('form.factory')
            ->willReturn($this->formFactory);
        $this->container
            ->expects($this->at(1))
            ->method('has')
            ->with('security.token_storage')
            ->willReturn(true);
        $this->container
            ->expects($this->at(2))
            ->method('get')
            ->with('security.token_storage')
            ->willReturn($this->storage);

        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);
        $json = '{ "label": "tes sd", "currency":"BTC", "value":8 }';
        $request->expects($this->once())->method('getContent')->willReturn($json);

        $expectedJson = '{"message":"User [test] added asset: "}';
        $this->assertSame($expectedJson, $this->controller->create($request)->getContent());
    }


    /** @test */
    public function createTestShouldTrowExceptionWhenFormInvalid()
    {
        $this->form->expects($this->once())->method('submit');
        $this->form->expects($this->once())->method('isValid')->willReturn(false);
        $this->form->expects($this->once())->method('getErrors')->willReturn([]);
        $this->form->expects($this->once())->method('all')->willReturn([]);

        $this->formFactory->expects($this->once())->method('create')->willReturn($this->form);

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('form.factory')
            ->willReturn($this->formFactory);

        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);
        $json = '{ "label": "tes sd", "currency":"BTC", "value":8 }';
        $request->expects($this->once())->method('getContent')->willReturn($json);

        $this->expectException(FormValidationException::class);
        $this->controller->create($request);
    }

    /** @test */
    public function updateTest()
    {

        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('getUsername')->willReturn('test');
        $user->expects($this->exactly(2))->method('getId')->willReturn('1');

        $asset = $this->createMock(Asset::class);
        $asset->expects($this->once())->method('getUser')->willReturn($user);

        $this->token->expects($this->once())->method('getUser')->willReturn($user);

        $this->form->expects($this->once())->method('submit');
        $this->form->expects($this->once())->method('isValid')->willReturn(true);
        $this->form->expects($this->once())->method('isSubmitted')->willReturn(true);

        $this->storage->expects($this->once())->method('getToken')->willReturn($this->token);

        $this->formFactory->expects($this->once())->method('create')->willReturn($this->form);

        $this->container
            ->expects($this->at(0))
            ->method('has')
            ->with('security.token_storage')
            ->willReturn(true);
        $this->container
            ->expects($this->at(1))
            ->method('get')
            ->with('security.token_storage')
            ->willReturn($this->storage);
        $this->container
            ->expects($this->at(2))
            ->method('get')
            ->with('form.factory')
            ->willReturn($this->formFactory);

        $this->repo->expects($this->once())->method('find')->willReturn($asset);
        /** @var MockObject|EntityManagerInterface $em */
        $this->em->expects($this->once())->method('getRepository')->willReturn($this->repo);
        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);

        $json = '{ "label": "tes sd", "currency":"BTC", "value":8 }';
        $request->expects($this->once())->method('getContent')->willReturn($json);

        $expectedJson = '{"message":"User [test] updated asset: "}';
        $this->assertSame($expectedJson, $this->controller->update($request, '1')->getContent());
    }

    /** @test */
    public function updateTestShouldTrowExceptionWhenFormInvalid()
    {

        $user = $this->createMock(User::class);
        $user->expects($this->exactly(2))->method('getId')->willReturn('1');

        $asset = $this->createMock(Asset::class);
        $asset->expects($this->once())->method('getUser')->willReturn($user);

        $this->token->expects($this->once())->method('getUser')->willReturn($user);

        $this->form->expects($this->once())->method('submit');
        $this->form->expects($this->once())->method('isValid')->willReturn(false);
        $this->form->expects($this->once())->method('getErrors')->willReturn([]);
        $this->form->expects($this->once())->method('all')->willReturn([]);

        $this->storage->expects($this->once())->method('getToken')->willReturn($this->token);

        $this->formFactory->expects($this->once())->method('create')->willReturn($this->form);

        $this->container
            ->expects($this->at(0))
            ->method('has')
            ->with('security.token_storage')
            ->willReturn(true);
        $this->container
            ->expects($this->at(1))
            ->method('get')
            ->with('security.token_storage')
            ->willReturn($this->storage);
        $this->container
            ->expects($this->at(2))
            ->method('get')
            ->with('form.factory')
            ->willReturn($this->formFactory);


        $this->repo->expects($this->once())->method('find')->willReturn($asset);
        $this->em->expects($this->once())->method('getRepository')->willReturn($this->repo);
        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);

        $json = '{ "label": "tes sd", "currency":"BTC", "value":8 }';
        $request->expects($this->once())->method('getContent')->willReturn($json);

        $this->expectException(FormValidationException::class);
        $expectedJson = '{"message":"User [test] updated asset: "}';
        $this->assertSame($expectedJson, $this->controller->update($request, '1')->getContent());
    }

    /** @test */
    public function updateTestShouldThrowExceptionWhenAssetFromOtherUer()
    {
        $user = $this->createMock(User::class);
        $user->expects($this->at(0))->method('getId')->willReturn('1');
        $user->expects($this->at(1))->method('getId')->willReturn('2');

        $asset = $this->createMock(Asset::class);
        $asset->expects($this->once())->method('getUser')->willReturn($user);

        $this->token->expects($this->once())->method('getUser')->willReturn($user);

        $this->storage->expects($this->once())->method('getToken')->willReturn($this->token);

        $this->container
            ->expects($this->at(0))
            ->method('has')
            ->with('security.token_storage')
            ->willReturn(true);
        $this->container
            ->expects($this->at(1))
            ->method('get')
            ->with('security.token_storage')
            ->willReturn($this->storage);

        $this->repo->expects($this->once())->method('find')->willReturn($asset);
        $this->em->expects($this->once())->method('getRepository')->willReturn($this->repo);
        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);
        $json = '{ "label": "tes sd", "currency":"BTC", "value":8 }';
        $request->expects($this->once())->method('getContent')->willReturn($json);

        $this->expectException(AssetException::class);
        $this->controller->update($request, '1');
    }

    /** @test */
    public function updateTestShouldThrowExceptionWhenAssetNotFound()
    {
        /** @var MockObject|AssetRepository $repo */
        $this->repo = $this->createMock(AssetRepository::class);
        $this->repo->expects($this->once())->method('find')->willReturn(null);
        $this->em->expects($this->once())->method('getRepository')->willReturn($this->repo);
        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);
        $json = '{ "label": "tes sd", "currency":"BTC", "value":8 }';
        $request->expects($this->once())->method('getContent')->willReturn($json);

        $this->expectException(AssetException::class);
        $this->controller->update($request, '1');
    }

    /** @test */
    public function deleteTest()
    {
        $user = $this->createMock(User::class);
        $user->expects($this->exactly(2))->method('getId')->willReturn('1');

        $asset = $this->createMock(Asset::class);
        $asset->expects($this->once())->method('getUser')->willReturn($user);
        $asset->expects($this->once())->method('getLabel')->willReturn('assetTest');

        $this->token->expects($this->once())->method('getUser')->willReturn($user);

        $this->storage->expects($this->once())->method('getToken')->willReturn($this->token);

        $this->container
            ->expects($this->at(0))
            ->method('has')
            ->with('security.token_storage')
            ->willReturn(true);
        $this->container
            ->expects($this->at(1))
            ->method('get')
            ->with('security.token_storage')
            ->willReturn($this->storage);

        $this->repo->expects($this->once())->method('find')->willReturn($asset);
        $this->em->expects($this->once())->method('getRepository')->willReturn($this->repo);
        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);

        $expectedJson = '{"message":"Asset [assetTest] was delete"}';
        $this->assertSame($expectedJson, $this->controller->delete($request, '1')->getContent());
    }

    /** @test */
    public function deleteTestShouldThrowExceptionWhenAssetFromOtherUer()
    {
        $user = $this->createMock(User::class);
        $user->expects($this->at(0))->method('getId')->willReturn('1');
        $user->expects($this->at(1))->method('getId')->willReturn('2');

        $asset = $this->createMock(Asset::class);
        $asset->expects($this->once())->method('getUser')->willReturn($user);
        $asset->expects($this->once())->method('getLabel')->willReturn('assetTest');

        $this->token->expects($this->once())->method('getUser')->willReturn($user);

        $this->storage->expects($this->once())->method('getToken')->willReturn($this->token);

        $this->container
            ->expects($this->at(0))
            ->method('has')
            ->with('security.token_storage')
            ->willReturn(true);
        $this->container
            ->expects($this->at(1))
            ->method('get')
            ->with('security.token_storage')
            ->willReturn($this->storage);

        $this->repo->expects($this->once())->method('find')->willReturn($asset);

        $this->em->expects($this->once())->method('getRepository')->willReturn($this->repo);
        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);

        $this->expectException(AssetException::class);
        $this->controller->delete($request, '1');
    }

    /** @test */
    public function getValuesTest()
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('getId')->willReturn('1');

        $this->token->expects($this->once())->method('getUser')->willReturn($user);

        $this->storage->expects($this->once())->method('getToken')->willReturn($this->token);

        $this->container
            ->expects($this->at(0))
            ->method('has')
            ->with('security.token_storage')
            ->willReturn(true);
        $this->container
            ->expects($this->at(1))
            ->method('get')
            ->with('security.token_storage')
            ->willReturn($this->storage);

        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);

        $this->service
            ->expects($this->once())
            ->method('getUserCurrencyValues')
            ->with('1', 'BTC')
            ->willReturn(['BRC' => '10']);
        $this->assertSame('{"BRC":"10"}', $this->controller->getValues($request, 'BTC')->getContent());
    }

    /** @test */
    public function getValuesTestShouldThrowValidProviderNotFoundException()
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('getId')->willReturn('1');

        $this->token->expects($this->once())->method('getUser')->willReturn($user);

        $this->storage->expects($this->once())->method('getToken')->willReturn($this->token);

        $this->container
            ->expects($this->at(0))
            ->method('has')
            ->with('security.token_storage')
            ->willReturn(true);
        $this->container
            ->expects($this->at(1))
            ->method('get')
            ->with('security.token_storage')
            ->willReturn($this->storage);

        /** @var MockObject|Request $request */
        $request = $this->createMock(Request::class);

        $this->service
            ->expects($this->once())
            ->method('getUserCurrencyValues')
            ->with('1', 'BTC')
            ->willThrowException(new ValidProviderNotFound);


        $this->controller->getValues($request, 'BTC');
    }
}