jQuery(document).ready(function($) {
	//Load more using Ajax
	$(document).on('click', '.sunset-load-more:not(.loading)', function(){
		var that = $(this);
		var page = that.data('page');
		var newpage = page+1;
		var ajaxurl = that.data('url');

		$('.sunset-load-more').addClass('loading').find('.text').slideUp(320);
		$('.sunset-load-more').find('.sunset-icon').addClass('spin');
		$.ajax({

			url: ajaxurl,
			type: 'post',
			data: {
				page: page,
				action: 'sunset_load_more'
			},
			error: function(response){
				console.log(response);
			},
			success: function(response){

				if(response == 0){
					$('.sunset-posts-container').append('<h3>No More Posts</h3>');
					$('.sunset-load-more').slideUp(320);
				}else{

					setTimeout(function(){
				that.data('page', newpage);
				$('.sunset-posts-container').append(response);
					$('.sunset-load-more').removeClass('loading').find('.text').slideDown(320);
					$('.sunset-load-more').find('.sunset-icon').removeClass('spin');
			}, 2000)

				}

			}

		});

	});


	
});