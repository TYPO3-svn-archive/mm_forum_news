mod.web_txmmforumM1 {

	defaultConfigFiles.mm_forum_news = EXT:mm_forum_news/ext_typoscript_constants.txt

	essentialConfiguration {
		mmforumnews_newsCommentCategory = 1
		mmforumnews_newsTopicAuthor     = 1
	}

	submodules {
		installation {
			categories {
				tt_news = MMFORUM_CONF_CATEGORY
				tt_news {
					icon  = EXT:mm_forum_news/res/img/mod/mmforum-conf.png
					name  = LLL:EXT:mm_forum_news/res/lang/locallang.xml:mod.category_title_short
					title = LLL:EXT:mm_forum_news/res/lang/locallang.xml:mod.category_title

					items {
						mmforumnews_newsCommentCategory = MMFORUM_CONF_ITEM
						mmforumnews_newsCommentCategory {
							type = special
							type.handler = EXT:mm_forum_news/lib/class.tx_mmforumnews_modinstall.php:tx_mmforumnews_ModInstall->getForumSelector

							label       = LLL:EXT:mm_forum_news/res/lang/locallang.xml:mod.newsCommentCategory.title
							description = LLL:EXT:mm_forum_news/res/lang/locallang.xml:mod.newsCommentCategory.desc
						}

						mmforumnews_newsTopicAuthor = MMFORUM_CONF_ITEM
						mmforumnews_newsTopicAuthor {
							type = group
							type.table = fe_users

							label       = LLL:EXT:mm_forum_news/res/lang/locallang.xml:mod.newsTopicAuthor.title
							description = LLL:EXT:mm_forum_news/res/lang/locallang.xml:mod.newsTopicAuthor.desc
						}

						mmforumnews_previewLength = MMFORUM_CONF_ITEM
						mmforumnews_previewLength {
							type = int
							label       = LLL:EXT:mm_forum_news/res/lang/locallang.xml:mod.previewLength.title
							description = LLL:EXT:mm_forum_news/res/lang/locallang.xml:mod.previewLength.desc
						}

						mmforumnews_previewAmount = MMFORUM_CONF_ITEM
						mmforumnews_previewAmount {
							type = int
							label       = LLL:EXT:mm_forum_news/res/lang/locallang.xml:mod.previewAmount.title
							description = LLL:EXT:mm_forum_news/res/lang/locallang.xml:mod.previewAmount.desc
						}
					}
				}
			}
		}
	}

}