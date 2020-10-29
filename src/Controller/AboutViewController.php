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

namespace Didapptic\Controller;

use Didapptic\Didapptic;
use Didapptic\Object\Application\Supporter\Supporter;
use Didapptic\Object\Constant\JavaScript;
use Didapptic\Object\Constant\View;
use Didapptic\Object\Environment;

/**
 * Class AboutViewController
 *
 * @package Didapptic\Controller
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class AboutViewController extends AbstractController {

    /** @var Environment|null $environment */
    private $environment = null;

    public function __construct(Environment $environment) {
        parent::__construct("Über Uns");
        $this->environment = $environment;
        $this->registerJavaScript(JavaScript::ABOUT_SCRIPT);
    }

    protected function onCreate(): void {

    }

    /**
     * @return string|null
     */
    protected function create(): ?string {

        $supporters = Didapptic::getSupporters();
        $template   = parent::loadTemplate(
            parent::getTemplatePath()
            , View::ABOUT_VIEW
        );

        /** @var Supporter $mainSupporter */
        $mainSupporter = $supporters->get("main");
        /** @var Supporter $devSupporter */
        $devSupporter = $supporters->get("dev");

        $baseUrl = Didapptic::getBaseURL(true);
        return $template->render([
            "pageDescription"        => "Auf didapptic finden Sie Apps für Ihren Unterricht oder teilen Ihre Erfahrung mit anderen. Bei didapptic handelt es sich um eine nicht-kommerzielle, didaktische App-Datenbank von Lehrenden für Lehrende! Auf dieser Seite finden Sie zahlreiche Apps für Android (verschiedene Hersteller) & iOS (iPhone, iPad), die Sie in Ihrer oder für Ihre Lehre einsetzen können. Zögern Sie nicht, Ihre Erfahrungen zu teilen und kontaktieren Sie uns!"
            , "iosHeader"            => "iOS Apps"
            , "aboutUsHeader"        => "Über uns"
            , "whatIsDidapptic"      => "Was ist didapptic?"
            , "furtherInformation"   => "Für weitere Informationen und Hinweise zur Nutzung und Funktionsweise von didapptic beachten Sie die "
            , "hintsLink"            => "$baseUrl/menu/hints/"
            , "hints"                => "Hinweise."
            , "appDescription"       => "Didapptic [Wortschöpfung aus didactic & app, gelesen: daɪˈdæptɪk] ist eine nicht-kommerzielle, didaktische App-Datenbank. Auf dieser Seite finden Sie Einträge für Apps (Android & iOS sowie Smartphone & Tablet), die Sie in Ihrer oder für Ihre Lehre einsetzen können."
            , "whoRunsDidapptic"     => "Wer betreibt didapptic.com und warum?"
            , "whoRunsDidappticDesc" => "Diese Webseite wird aus wissenschaftlichem und pädagogisch-didaktischen Interesse heraus betrieben. <a href=\"http://camuka.de\" target=\"_blank\">Ahmet Camuka</a>, Initiator und Betreiber dieser Seite, ist Lehrer für die Fächer Kunst und Mathematik. Die Idee zu dieser App-Datenbank erwuchs aus seiner Forschung zum Einsatz von digitalen mobilen Medien in der Kunstpädagogik. Es zeigt sich, dass die Relevanz o. g. Medien in allen Fächern stetig steigt. Nicht nur in der Kunstpädagogik, die ähnlich wie die Bildende Kunst, die sich immer mit neuen Mitteln und Medien der bildnerischen bzw. künstlerischen Gestaltung auseinandersetzt, werden Smartphones und Tablets vielfältig eingesetzt. In Fächern wie Englisch, Mathematik und Naturwissenschaften werden diese ebenso eingesetzt und in sinnvolle didaktische Konzepte eingebettet. Damit dies möglichst gut gelingt und Lehrende wie Lernende von ihren jeweiligen Erfahrungen gegenseitig profitieren können, wurde diese Plattform geschaffen. Sie soll einen möglichst einfachen, gut verständlichen und informativen Austausch gewährleisten; <a href=\"https://didapptic.com/index.php/menu/hints/\" target=\"_blank\">hier</a> erhalten Sie weitere Informationen zum Aufbau und der funktionsweise der Plattform. Interessierte Lehrende haben die Möglichkeit, ebenso <a href=\"https://didapptic.com/index.php/menu/login/\" target=\"_blank\">Apps einzutragen</a>. Falls Sie noch nicht registriert sind, erhalten Sie auf Nachfrage gerne eine Zugangsberechtigung. Wir verwenden bewusst keine Affiliate-Links und erhalten keine Provisionen für App-Einträge, eine Profitorientierung erschiene unglaubwürdig. Wir verpflichten uns und alle registrierten Mitglieder, App-Bewertungen an sachlichen Gesichtspunkten orientiert zu formulieren."
            , "license"              => "Lizenz"            , "supportDesc1"         => "Das Projekt wird durch den "
            , "mainSupporterLink"    => $mainSupporter->getUrl()
            , "mainSupporterName"    => $mainSupporter->getName()
            , "supportDesc2"         => "unterstützt."
            , "devSupporterLink"     => $devSupporter->getUrl()
            , "devSupporterName"     => $devSupporter->getName()
            , "devDesc"              => "unterstützt die Entwicklung der Plattform als Software-Entwickler."
        ]);
    }

    protected function onDestroy(): void {

    }

}
