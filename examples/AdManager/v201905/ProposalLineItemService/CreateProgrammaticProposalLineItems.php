<?php
/**
 * Copyright 2017 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\AdsApi\Examples\AdManager\v201905\ProposalLineItemService;

require __DIR__ . '/../../../../vendor/autoload.php';

use DateTime;
use DateTimeZone;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\Util\v201905\AdManagerDateTimes;
use Google\AdsApi\AdManager\v201905\AdExchangeEnvironment;
use Google\AdsApi\AdManager\v201905\Goal;
use Google\AdsApi\AdManager\v201905\LineItemType;
use Google\AdsApi\AdManager\v201905\Money;
use Google\AdsApi\AdManager\v201905\ProposalLineItem;
use Google\AdsApi\AdManager\v201905\ProposalLineItemMarketplaceInfo;
use Google\AdsApi\AdManager\v201905\RateType;
use Google\AdsApi\AdManager\v201905\ServiceFactory;
use Google\AdsApi\AdManager\v201905\UnitType;
use Google\AdsApi\Common\OAuth2TokenBuilder;

/**
 * Creates a programmatic proposal line item for a sales-enabled network.
 *
 * This example is meant to be run from a command line (not as a webpage) and
 * requires that you've setup an `adsapi_php.ini` file in your home directory
 * with your API credentials and settings. See `README.md` for more info.
 */
class CreateProgrammaticProposalLineItems
{

    // Set the proposal, product, and rate card ID to use when creating the
    // proposal line item.
    const PROPOSAL_ID = 'INSERT_PROPOSAL_ID_HERE';
    const PRODUCT_ID = 'INSERT_PRODUCT_ID_HERE';
    const RATE_CARD_ID = 'INSERT_RATE_CARD_ID_HERE';

    public static function runExample(
        ServiceFactory $serviceFactory,
        AdManagerSession $session,
        $proposalId,
        $productId,
        $rateCardId
    ) {
        $proposalLineItemService =
            $serviceFactory->createProposalLineItemService($session);

        // Create a standard proposal line item.
        $proposalLineItem = new ProposalLineItem();
        $proposalLineItem->setName('Proposal line item #' . uniqid());
        $proposalLineItem->setProposalId($proposalId);
        $proposalLineItem->setRateCardId($rateCardId);
        $proposalLineItem->setProductId($productId);
        $proposalLineItem->setLineItemType(LineItemType::STANDARD);

        // Set the Marketplace information.
        $marketplaceInfo = new ProposalLineItemMarketplaceInfo();
        $marketplaceInfo->setAdExchangeEnvironment(
            AdExchangeEnvironment::DISPLAY
        );
        $proposalLineItem->setMarketplaceInfo($marketplaceInfo);

        // Set the length of the proposal line item to run.
        $proposalLineItem->setStartDateTime(
            AdManagerDateTimes::fromDateTime(
                new DateTime('now', new DateTimeZone('America/New_York'))
            )
        );
        $proposalLineItem->setEndDateTime(
            AdManagerDateTimes::fromDateTime(
                new DateTime('+1 month', new DateTimeZone('America/New_York'))
            )
        );

        // Set pricing for the proposal line item for 1000 impressions at a CPM
        // of $2 for a total value of $2.
        $goal = new Goal();
        $goal->setUnits(1000);
        $goal->setUnitType(UnitType::IMPRESSIONS);
        $proposalLineItem->setGoal($goal);
        $proposalLineItem->setNetCost(new Money('USD', 2000000));
        $proposalLineItem->setNetRate(new Money('USD', 2000000));
        $proposalLineItem->setRateType(RateType::CPM);

        // Create the proposal line items on the server.
        $results = $proposalLineItemService->createProposalLineItems(
            [$proposalLineItem]
        );

        // Print out some information for each created proposal line item.
        foreach ($results as $i => $proposalLineItem) {
            printf(
                "%d) Programmatic proposal line item with ID %d and name '%s'"
                . " was created.%s",
                $i,
                $proposalLineItem->getId(),
                $proposalLineItem->getName(),
                PHP_EOL
            );
        }
    }

    public static function main()
    {
        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile()
            ->build();

        // Construct an API session configured from an `adsapi_php.ini` file
        // and the OAuth2 credentials above.
        $session = (new AdManagerSessionBuilder())->fromFile()
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        self::runExample(
            new ServiceFactory(),
            $session,
            intval(self::PROPOSAL_ID),
            intval(self::PRODUCT_ID),
            intval(self::RATE_CARD_ID)
        );
    }
}

CreateProgrammaticProposalLineItems::main();
