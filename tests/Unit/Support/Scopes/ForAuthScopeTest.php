<?php

namespace Tests\Unit\Support\Scopes;

use Domain\Candidate\Models\Candidate;
use Domain\Organization\Models\Employee;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Support\Scopes\ForAuthScope;
use Tests\TestCase;

class ForAuthScopeTest extends TestCase
{
    protected ForAuthScope $scope;

    protected array $methods;

    protected \Closure $closure;

    public function setUp(): void
    {
        parent::setUp();

        $this->scope = new ForAuthScope();

        $this->closure = function (Builder $builder) {
            return $builder;
        };

        $this->methods = [
            'forSintUser' => fn (Builder $builder) => $builder->where('sint_user_id', 1),
            'forOrganizationEmployee' => fn (Builder $builder) => $builder->where('employee_id', 1),
            'forCandidate' => fn (Builder $builder) => $builder->where('candidate_id', 1),
            'forGuest' => fn (Builder $builder) => $builder->where('guest_id', 1),
        ];

        Auth::shouldReceive('runningUnitTests')->andReturn(true);
    }

    /** @test  */
    public function itShouldHaveMethodsForEachAuthUserAndEachMethodReturnInstanceOfAuthScope(): void
    {
        foreach ($this->methods as $method => $closure) {
            $this->assertTrue(
                method_exists($this->scope, $method),
                sprintf("class %s doesn't have method: %s", $this->scope::class, $method)
            );

            $this->assertTrue(
                $this->scope->$method($closure) instanceof $this->scope,
                sprintf('method: %s should return instance of itself', $method)
            );
        }
    }

    /** @test  */
    public function itShouldFillBuilderBerUserArray(): void
    {
        $scopeClass = new \ReflectionClass($this->scope);

        foreach ($this->methods as $method => $closure) {
            $method = $scopeClass->getMethod($method);
            $method->invokeArgs($this->scope, [$closure]);
        }

        $result = $scopeClass->getProperty('buildersBerUser')->getValue($this->scope);

        $this->assertIsArray($result);

        foreach (['user', 'employee', 'candidate', '__guest__'] as $key) {
            $this->assertArrayHasKey($key, $result);
        }
    }

    /** @test  */
    public function itShouldOnlyApplyCandidateBuilderWhenAuthIsCandidate(): void
    {
        Auth::shouldReceive('api-candidate')->andReturnSelf()
            ->shouldReceive('user')->andReturn(new Candidate(['email' => 'candidate@test.com']))
            ->shouldReceive('check')->andReturn(true)
            ->shouldReceive('guest')->andReturn(false);

        $this->assertSame($this->applyScope()->toRawSql(),
            'select * from `users` where `candidate_id` = 1'
        );
    }

    /** @test  */
    public function itShouldOnlyApplyOrganizationEmployeeBuilderWhenAuthIsEmployee(): void
    {
        Auth::shouldReceive('api-organization')->andReturnSelf()
            ->shouldReceive('user')->andReturn(new Employee(['email' => 'employee@test.com']))
            ->shouldReceive('check')->andReturn(true)
            ->shouldReceive('guest')->andReturn(false);

        $this->assertSame($this->applyScope()->toRawSql(),
            'select * from `users` where `employee_id` = 1');
    }

    /** @test  */
    public function itShouldOnlyApplyGuestBuilderWhenGuest(): void
    {
        Auth::shouldReceive('api')->andReturnSelf()
            ->shouldReceive('user')->andReturn('')
            ->shouldReceive('check')->andReturn(false)
            ->shouldReceive('guest')->andReturn(true);

        $this->assertSame($this->applyScope()->toRawSql(),
            'select * from `users` where `guest_id` = 1'
        );
    }

    /** @test  */
    public function itShouldOnlyApplySintUserBuilderWhenAuthIsSintUser(): void
    {
        Auth::shouldReceive('api')->andReturnSelf()
            ->shouldReceive('user')->andReturn(new User(['email' => 'admin@sint.com']))
            ->shouldReceive('check')->andReturn(true)
            ->shouldReceive('guest')->andReturn(false);

        $this->assertSame($this->applyScope()->toRawSql(),
            'select * from `users` where `sint_user_id` = 1'
        );
    }

    private function applyScope(Model $testing_model = new User()): Builder
    {
        $builder = $testing_model->newQuery();

        foreach ($this->methods as $method => $closure) {
            $this->scope->$method($closure);
        }

        $this->scope->apply($builder, $testing_model);

        return $builder;
    }
}
