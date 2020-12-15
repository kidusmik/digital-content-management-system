<div class="col-lg-9">
	<div class="well">
		<div class="row">
			<div class="col-lg-12">
			<?php
				foreach($contents->result() as $music){
					echo '<div class="col-lg-offset-1 col-lg-8">';
					echo '<div class="music-list well"><span class="name-text">'.$music->content_name.'</span>';
					echo '<br /><a href="/music/view/'.$music->content_id.'"><span class="play-music glyphicon glyphicon-play-circle"></span></a>';
					for($i=0; $i<5; $i++){
						if($music->rating > $i){
							echo '<span class="glyphicon glyphicon-star"></span>';
						}
						else{
							echo '<span class="glyphicon glyphicon-star-empty"></span>';
						}
					}
					echo '<button  id="'.$music->content_id.'" class="btn btn-link" onclick="delete_creator_content(this, \'music\')"><span class="glyphicon glyphicon-trash"></span></button>';
					echo '<button id="'.$music->content_id.'" data-toggle="modal" data-target="#edit-modal" class="edit btn btn-link" onclick="edit_creator_content(this, \'music\', \'modal\')">
							<span class="glyphicon glyphicon-edit"></span></button>';
					echo '</div></div>';
				}
			?>
			</div>
			<div class="col-lg-offset-5 col-lg-2">
				<a type="button" class="btn btn-default" href="/creator/music/upload">
					<span class="glyphicon glyphicon-upload"></span>
					upload music
				</a>
			</div>
		</div>
	</div>
</div>
</div>