<?php

namespace App\Tests\Unit\Infrastructure;

use App\Core\Components\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use App\Infrastructure\Token\Services\TokenService;
use App\Tests\Unit\AbstractTestCase;

final class TokenServiceTest extends AbstractTestCase
{
    private TokenService $tokenService;

    private const TOKEN = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiY29tcGFueSI6MX0.eOe0BAe5N9aCCdwB7ETFrrOpQaHSWJUsejayELA-SmU';

    protected function setUp(): void
    {
        $this->tokenService = new TokenService();
        parent::setUp();
    }

    public function test_decode(): void
    {
        $request = new Request();
        $request->headers->set('Authorization', self::TOKEN);
        $decodedToken = $this->tokenService->decode($request);

        self::assertEquals(1, $decodedToken['id']);
    }
}
