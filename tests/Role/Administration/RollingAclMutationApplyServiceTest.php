<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Administration;

use App\Rolling\Service\Administration\RollingAclMutationApplyService;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationApplyRequestBuilderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationExecutionGatewayInterface;
use App\Rolling\Value\Administration\RollingAclMutationApplyRequest;
use App\Rolling\Value\Administration\RollingAclMutationResult;
use App\Rolling\Value\Administration\RollingAclMutationReview;
use PHPUnit\Framework\TestCase;

final class RollingAclMutationApplyServiceTest extends TestCase
{
    public function testApplyReviewedMutationBuildsRequestAndDelegatesExecution(): void
    {
        $builder = new class implements RollingAclMutationApplyRequestBuilderInterface {
            public function fromReview(
                string $requestKey,
                RollingAclMutationReview $review,
                string $requestedBySubject,
            ): RollingAclMutationApplyRequest {
                return RollingAclMutationApplyRequest::fromReview(
                    $requestKey,
                    $review,
                    $requestedBySubject,
                    ['builder' => 'test'],
                );
            }
        };

        $gateway = new class implements RollingAclMutationExecutionGatewayInterface {
            public function execute(RollingAclMutationApplyRequest $request): RollingAclMutationResult
            {
                return RollingAclMutationResult::success('accepted', [
                    'request_key' => $request->requestKey(),
                    'review_valid' => $request->reviewValid(),
                    'safe_context' => $request->safeContext(),
                ]);
            }
        };

        $service = new RollingAclMutationApplyService($builder, $gateway);
        $review = new RollingAclMutationReview(
            'acl.allow',
            'user:42',
            'rolling.permission.manage',
            'scope:rolling',
            true,
            ['Validate request.'],
            ['No warnings.'],
            [],
            ['source' => 'test'],
        );

        $result = $service->applyReviewedMutation('req-123', $review, 'administering:tester');

        self::assertTrue($result->succeeded());
        self::assertSame('succeeded', $result->status());
        self::assertSame('accepted', $result->safeMessage());
        self::assertSame('req-123', $result->safeContext()['request_key']);
        self::assertTrue($result->safeContext()['review_valid']);
        self::assertSame('test', $result->safeContext()['safe_context']['builder']);
    }
}
