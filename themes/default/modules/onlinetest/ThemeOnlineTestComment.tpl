<!-- BEGIN: load_comment -->
<!-- BEGIN: loop -->
	<li class="media" id="group-{LOOP.comment_id}">
		<a class="pull-left" href="javascript:void(0);">
			<img class="media-object bg-gainsboro" src="{LOOP.photo}" alt="{LOOP.username}" width="40"/>
		</a>
		<div class="media-body">
			<div class="contentComment">{LOOP.comment}</div>
			<div class="comment-info clearfix">
				<i class="pull-left fa fa-user"></i> <span class="cm_item">{LOOP.post_name} </span>
				<i class="fa fa-clock-o"></i> <span class="small">{LOOP.date_added}</span>
				<span class="small"><a href="javascript:void(0);" class="canEdit" data-question_id="{DATA.question_id}" data-comment_id="{LOOP.comment_id_token}" data-token="{DATA.token}"><i class="fa fa-edit fa-1x fa-fw"></i> {LANG.edit}</a></span>
				<span class="small"><a href="javascript:void(0);" class="canDelete" data-question_id="{DATA.question_id}" data-comment_id="{LOOP.comment_id_token}" data-token="{DATA.token}"><i class="fa fa-trash fa-1x fa-fw"></i> {LANG.delete}</a></span>
			</div>				
		</div>
	</li>
<!-- END: loop -->
<!-- END: load_comment -->

<!-- BEGIN: main -->
<ul class="comment-list">
	<!-- BEGIN: loop -->
	<li class="media" id="group-{LOOP.comment_id}">
		<a class="pull-left" href="javascript:void(0);">
			<img class="media-object bg-gainsboro" src="{LOOP.photo}" alt="{LOOP.username}" width="40"/>
		</a>
		<div class="media-body">
			<div class="contentComment">{LOOP.comment}</div>
			<div class="comment-info clearfix">
				<i class="pull-left fa fa-user"></i> <span class="cm_item">{LOOP.post_name} </span>
				<i class="fa fa-clock-o"></i> <span class="small">{LOOP.date_added}</span>
				<span class="small"><a href="javascript:void(0);" class="canEdit" data-question_id="{DATA.question_id}" data-comment_id="{LOOP.comment_id_token}" data-token="{DATA.token}"><i class="fa fa-edit fa-1x fa-fw"></i> {LANG.edit}</a></span>
				<span class="small"><a href="javascript:void(0);" class="canDelete" data-question_id="{DATA.question_id}" data-comment_id="{LOOP.comment_id_token}" data-token="{DATA.token}"><i class="fa fa-trash fa-1x fa-fw"></i> {LANG.delete}</a></span>
			</div>
						
		</div>
	</li>
	<!-- END: loop -->
	
</ul>
<!-- BEGIN: loadmore -->
<div class="loadmore">
	<a href="javascript:void(0);" class="loadMoreComment" data-question_id="{DATA.question_id}" data-token="{DATA.token}" data-page="{PAGE}"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> {LANG.more_comment}</a> 
</div> 
<!-- END: loadmore -->
<div class="boxEditor">
	<div class="media-body">	 
		<div id="insertComment-{DATA.question_id}" data-id="{DATA.question_id}" >
			<input type="hidden" name="comment_id" value="0" />
			<input type="hidden" name="question_id" value="{DATA.question_id}" />
			<input type="hidden" name="token" value="{DATA.token}" />
			<input type="hidden" name="lastcomment" value="{LAST_TIME}" />
			<div class="comment-info clearfix">
				<textarea class="form-control" id="comment-{DATA.question_id}" name="comment-{DATA.question_id}" rows="4" cols="50"> </textarea>
			</div>
			<div class="comment-button clearfix">
				 <a class="btn btn-primary insertComment"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> {LANG.send_comment}</a>
			</div>
		</div>
	</div>
</div> 
<!-- END: main -->