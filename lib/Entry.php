<?php
/**
 * Concos - Calibre on Nextcloud OPDS Server
 *
 * @author Claas Lisowski
 * @copyright 2018 Claas Lisowski
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 */

namespace OCA\Calibre_opds;

class Entry {
    public $title;
    public $id;
    public $content;
    public $numberOfElement;
    public $contentType;
    public $linkArray;
    public $localUpdated;
    public $className;
    private static $updated = NULL;

    public static $icons = array(
        Author::ALL_AUTHORS_ID => 'images/author.png',
        Serie::ALL_SERIES_ID => 'images/serie.png',
        Book::ALL_RECENT_BOOKS_ID => 'images/recent.png',
        Tag::ALL_TAGS_ID => 'images/tag.png',
        Language::ALL_LANGUAGES_ID => 'images/language.png',
        Rating::ALL_RATING_ID => 'images/rating.png',
        "cops:books$" => 'images/allbook.png',
        "cops:books:letter" => 'images/allbook.png',
        Publisher::ALL_PUBLISHERS_ID => 'images/publisher.png',
    );

    public function getUpdatedTime() {
        if (!is_null($this->localUpdated)) {
            return date(DATE_ATOM, $this->localUpdated);
        }
        if (is_null(self::$updated)) {
            self::$updated = time();
        }
        return date(DATE_ATOM, self::$updated);
    }

    public function getNavLink() {
        foreach ($this->linkArray as $link) {
            /* @var $link LinkNavigation */

            if ($link->type != Link::OPDS_NAVIGATION_TYPE) {continue;}

            return $link->hrefXhtml();
        }
        return "#";
    }

    public function __construct($ptitle, $pid, $pcontent, $pcontentType, $plinkArray, $pclass = "", $pcount = 0) {
        $this->title = $ptitle;
        $this->id = $pid;
        $this->content = $pcontent;
        $this->contentType = $pcontentType;
        $this->linkArray = $plinkArray;
        $this->className = $pclass;
        $this->numberOfElement = $pcount;

        if (Config::get('concos_show_icons', 'true') === 'true') {
            foreach (self::$icons as $reg => $image) {
                if (preg_match("/" . $reg . "/", $pid)) {
                    array_push($this->linkArray, new Link(getUrlWithVersion($image), "image/png", Link::OPDS_THUMBNAIL_TYPE));
                    break;
                }
            }
        }

        if (!is_null(GetUrlParam(DB))) {
            $this->id = str_replace("cops:", "cops:" . GetUrlParam(DB) . ":", $this->id);
        }

    }
}