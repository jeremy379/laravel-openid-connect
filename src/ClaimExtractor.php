<?php

namespace OpenIDConnect;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use OpenIDConnect\Claims\ClaimSet;
use OpenIDConnect\Claims\ClaimSetInterface;
use OpenIDConnect\Exceptions\ProtectedScopeException;

class ClaimExtractor
{
    /**
     * @var ClaimSetInterface[]
     */
    protected array $claimSets;

    /**
     * @param ClaimSetInterface[] $claimSets
     * @throws ProtectedScopeException
     */
    public function __construct(ClaimSetInterface ...$claimSets)
    {
        $this
            ->addClaimSet($this->profile())
            ->addClaimSet($this->email())
            ->addClaimSet($this->address())
            ->addClaimSet($this->phone());

        foreach ($claimSets as $claimSet) {
            $this->addClaimSet($claimSet);
        }
    }

    /**
     * @return string[]
     */
    public function getProtectedClaims(): array
    {
        return ['profile', 'email', 'address', 'phone'];
    }

    /** @throws ProtectedScopeException */
    public function addClaimSet(ClaimSetInterface $claimSet): self
    {
        $scope = $claimSet->getScope();

        if (in_array($scope, $this->getProtectedClaims()) && !empty($this->claimSets[$scope])) {
            throw new ProtectedScopeException($scope);
        }
        $this->claimSets[$scope] = $claimSet;

        return $this;
    }

    public function getClaimSet(string $scope): ?ClaimSetInterface
    {
        return $this->hasClaimSet($scope) ? $this->claimSets[$scope] : null;
    }

    public function hasClaimSet(string $scope): bool
    {
        return array_key_exists($scope, $this->claimSets);
    }

    /**
     * @param string[]|ScopeEntityInterface[] $scopes
     * @param string[] $claims
     * @return string[]
     */
    public function extract(array $scopes, array $claims): array
    {
        $extracted = [];
        foreach ($scopes as $scope) {
            if ($scope instanceof ScopeEntityInterface) {
                $scope = $scope->getIdentifier();
            }

            if (!$claimSet = $this->getClaimSet($scope)) {
                continue;
            }

            $intersected = array_intersect($claimSet->getClaims(), array_keys($claims));

            $extracted = array_merge(
                $extracted,
                array_filter($claims, function ($key) use ($intersected) {
                    return in_array($key, $intersected);
                }, ARRAY_FILTER_USE_KEY)
            );
        }
        return $extracted;
    }

    private function profile(): ClaimSetInterface
    {
        return new ClaimSet('profile', [
            'name',
            'family_name',
            'given_name',
            'middle_name',
            'nickname',
            'preferred_username',
            'profile',
            'picture',
            'website',
            'gender',
            'birthdate',
            'zoneinfo',
            'locale',
            'updated_at',
        ]);
    }

    private function email(): ClaimSetInterface
    {
        return new ClaimSet('email', [
            'email',
            'email_verified',
        ]);
    }

    private function address(): ClaimSetInterface
    {
        return new ClaimSet('address', [
            'address',
        ]);
    }

    private function phone(): ClaimSetInterface
    {
        return new ClaimSet('phone', [
            'phone_number',
            'phone_number_verified',
        ]);
    }
}
