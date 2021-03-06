<?php

final class MoegirlRatingHooks {
	public static function onSkinAfterContent( &$data, $skin ) {
		$pageTitle = $skin->getTitle();
		$output = $skin->getOutput();
		$request = $skin->getRequest();


		MRLogging::logging( MRLogging::$INFO, __FILE__, __LINE__, 'Namespace: '. $pageTitle->getNamespace() );

		if ( $pageTitle->isSpecialPage()
			|| $pageTitle->getArticleID() == 0
			|| !$pageTitle->canTalk()
			|| $pageTitle->isTalkPage()
			|| method_exists( $pageTitle, 'isMainPage' ) && $pageTitle->isMainPage() // 主页
			|| in_array( $pageTitle->getNamespace(), array( NS_MEDIAWIKI, NS_TEMPLATE, NS_CATEGORY, NS_FILE, NS_USER ))
			|| $output->isPrintable()
			|| $request->getVal( 'action', 'view' ) != 'view' 
			) {

			return true;
		}


		$articleId = $skin->getTitle()->getArticleID();
		MRLogging::logging( MRLogging::$INFO, __FILE__, __LINE__, 'Moegirl rating show in wiki: ' . $articleId );

		global $wgScriptPath;

		$data .=<<<EOF
<div id="rating-main">
  <div class="moegirl_rating clearfix">
    <div class="rating_title">为本页面评分:</div>
    <div class="rating_body_disabled rating_main" >
      <ul class="mr-rating-ul">
        <li><a class="r-1" >1</a></li>
        <li><a class="r-2" >2</a></li>
        <li><a class="r-3" >3</a></li>
        <li><a class="r-4" >4</a></li>
        <li><a class="r-5" >5</a></li>
      </ul>

      <div class="rating_body_result" ></div>
	  <div class="moegirl-rating-error-tip"></div>
    </div>
    <div class="rating_result" ><div class="result_icon loading" ></div><div class="result_text" ></div></div>
  </div>
</div>
<script type="text/javascript" >
window.RLQ = window.RLQ || [];
window.RLQ.push(function(){
	mw.loader.using( 'ext.MoegirlRating', function() {
		new MoegirlRatingControl( '#rating-main', $articleId, '$wgScriptPath' ).init();
	});
});
</script>
EOF;

		return true;
	}

	public static function addDatabases( DatabaseUpdater $updater ) {
		$updater->addExtensionUpdate( array( 'addTable', SqlSentences::$ratingRecordTable, __DIR__  . '/sql/create-rating-history-table.sql', true ) );

		return true;
	}
}
