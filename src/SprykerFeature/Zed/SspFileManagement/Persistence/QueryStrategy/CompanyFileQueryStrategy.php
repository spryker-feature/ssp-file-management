<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerFeature\Zed\SspFileManagement\Persistence\QueryStrategy;

use Generated\Shared\Transfer\FileAttachmentFileCriteriaTransfer;
use Orm\Zed\FileManager\Persistence\SpyFileQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use SprykerFeature\Shared\SspFileManagement\Plugin\Permission\ViewCompanyFilesPermissionPlugin;
use SprykerFeature\Shared\SspFileManagement\SspFileManagementConfig;

class CompanyFileQueryStrategy implements FilePermissionQueryStrategyInterface
{
    /**
     * @param \Orm\Zed\FileManager\Persistence\SpyFileQuery $query
     * @param \Generated\Shared\Transfer\FileAttachmentFileCriteriaTransfer $fileAttachmentFileCriteriaTransfer
     *
     * @return \Orm\Zed\FileManager\Persistence\SpyFileQuery
     */
    public function apply(SpyFileQuery $query, FileAttachmentFileCriteriaTransfer $fileAttachmentFileCriteriaTransfer): SpyFileQuery
    {
        $query
            ->_or()
            ->useSpyCompanyFileQuery(null, Criteria::LEFT_JOIN)
                ->useCompanyQuery(null, Criteria::LEFT_JOIN)
                    ->useCompanyUserQuery(null, Criteria::LEFT_JOIN)
                        ->filterByIdCompanyUser($fileAttachmentFileCriteriaTransfer->getCompanyUserOrFail()->getIdCompanyUserOrFail())
                    ->endUse()
                ->endUse()
            ->endUse();

        return $query;
    }

    /**
     * @return string
     */
    public function getPermissionKey(): string
    {
        return ViewCompanyFilesPermissionPlugin::KEY;
    }

    /**
     * @param \Generated\Shared\Transfer\FileAttachmentFileCriteriaTransfer $fileAttachmentFileCriteriaTransfer
     *
     * @return bool
     */
    public function isApplicable(FileAttachmentFileCriteriaTransfer $fileAttachmentFileCriteriaTransfer): bool
    {
        $entityTypes = $fileAttachmentFileCriteriaTransfer->getFileAttachmentFileConditionsOrFail()->getEntityTypes();

        return in_array(SspFileManagementConfig::ENTITY_TYPE_COMPANY, $entityTypes, true);
    }
}
