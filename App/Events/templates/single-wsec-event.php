<?php
/*
Template Name: Event
Template Post Type: wsec-event
*/

if (get_header('desktop') === false) {
	get_header();
} else {
	get_header('desktop');
}

global $post;
$meta = \get_post_meta($post->ID);
$image = wp_get_attachment_image($meta['_thumbnail_id'][0], [200, 200]);
$startDate = strtotime($meta['wsec_start_event_date'][0]);
$endDate = strtotime($meta['wsec_end_event_date'][0]);
?>


<main class="main">
	<div class='events-list'>
		<div class='event'>
			<h2 class='title'><?php echo esc_html($post->post_title); ?></h2>
			<div class='columns'>
				<div class='column'>
					<div class='event-image'><?php echo wp_kses_post($image); ?></div>
				</div>
				<div class='column'>
					<div class="event-info">
						<h3 class='sub-title'><?php echo esc_html($meta['wsec_subtitle'][0]); ?></h3>
						<p class='start-date'><?php echo esc_html__('Start: ', 'web-systems-events-calendar') . esc_html(date('Y-m-d h:i:s', $startDate)); ?></p>
						<p class='end-date'><?php echo esc_html__('End: ', 'web-systems-events-calendar') . esc_html(date('Y-m-d h:i:s', $endDate)); ?></p>
						<p class='time-left'></p>
						<p class='description'><?php echo wp_kses_post($post->post_content); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</main><!-- #main -->

<?php get_footer();
