<?php
declare(strict_types=1);
/**
 * MIT License
 *
 * Copyright (c) 2020 didapptic, <info@didapptic.com>
 *
 * @author Dogan Ucar <dogan@dogan-ucar.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Didapptic\Service\App\Metadata;

use Didapptic\Repository\CategoryRepository;
use Didapptic\Repository\PrivacyRepository;
use Didapptic\Repository\RecommendationRepository;
use Didapptic\Repository\StudySubjectRepository;
use Didapptic\Repository\TagRepository;

/**
 * Class MetadataService
 *
 * @package Didapptic\Service\App\Metadata
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class MetadataService {

    public const CONTEXT_CATEGORY       = "category";
    public const CONTEXT_TAG            = "tag";
    public const CONTEXT_PRIVACY        = "privacy";
    public const CONTEXT_RECOMMENDATION = "recommendation";
    public const CONTEXT_SUBJECT        = "subject";

    /** @var TagRepository */
    private $tagManager;
    /** @var PrivacyRepository */
    private $privacyManager;
    /** @var RecommendationRepository */
    private $recommendationManager;
    /** @var StudySubjectRepository */
    private $subjectManager;
    /** @var CategoryRepository */
    private $categoryManager;

    public function __construct(
        TagRepository $tagManager
        , PrivacyRepository $privacyManager
        , RecommendationRepository $recommendationManager
        , StudySubjectRepository $subjectManager
        , CategoryRepository $categoryManager
    ) {
        $this->tagManager            = $tagManager;
        $this->privacyManager        = $privacyManager;
        $this->recommendationManager = $recommendationManager;
        $this->subjectManager        = $subjectManager;
        $this->categoryManager       = $categoryManager;
    }

    public function getMetadata(string $context): array {
        $data = [];
        switch ($context) {
            case MetadataService::CONTEXT_CATEGORY:
                $data = $this->categoryManager->getCategories();
                break;
            case MetadataService::CONTEXT_TAG:
                $data = $this->tagManager->getTags();
                break;
            case MetadataService::CONTEXT_PRIVACY:
                $data = $this->privacyManager->getPrivacy();
                break;
            case MetadataService::CONTEXT_RECOMMENDATION:
                $data = $this->recommendationManager->getRecommendations();
                break;
            case MetadataService::CONTEXT_SUBJECT:
                $data = $this->subjectManager->getSubjects();
                break;
        }
        return $data;
    }

}
