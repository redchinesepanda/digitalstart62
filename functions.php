<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

require_once('ds62-elementor-query.php');
require_once('ds62-acf.php');
require_once('ds62-wpuf.php');
require_once('ds62-other.php');
require_once('ds62-ajax.php');
require_once('ds62-live-comments.php');
require_once('ds62-wpuf-user-listing.php');
require_once('ds62-shortcode.php');
require_once('ds62-preset.php');

/*Переопределение вёрстки блока комментариев в модальном окне задач*/

function astra_theme_comment( $comment, $args, $depth ) {
	switch ( $comment->comment_type ) {

		case 'pingback':
		case 'trackback':
			// Display trackbacks differently than normal comments.
			?>
			<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
				<p><?php esc_html_e( 'Pingback:', 'astra' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'astra' ), '<span class="edit-link">', '</span>' ); ?></p>
			</li>
			<?php
			break;

		default:
			// Proceed with normal comments.
			global $post;
			?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

				<article id="comment-<?php comment_ID(); ?>" class="ast-comment">
				<div class= 'ast-comment-info'>
					<div class='ast-comment-avatar-wrap ast-test'><?php echo get_avatar( $comment, 50 ); ?></div><!-- Remove 1px Space
					-->
							<?php
							astra_markup_open( 'ast-comment-data-wrap' );
							astra_markup_open( 'ast-comment-meta-wrap' );
							echo '<header ';
							echo astra_attr(
								'commen-meta-author',
								array(
									'class' => 'ast-comment-meta ast-row ast-comment-author vcard capitalize',
								)
							);
							echo '>';

								printf(
									astra_markup_open(
										'ast-comment-cite-wrap',
										array(
											'open'  => '<div %s>',
											'class' => 'ast-comment-cite-wrap',
										)
									) . '<cite><b class="fn">%1$s</b> %2$s</cite></div>',
									get_comment_author_link(),
									// If current post author is also comment author, make it known visually.
									( $comment->user_id === $post->post_author ) ? '<span class="ast-highlight-text ast-cmt-post-author"></span>' : ''
								);

							

							?>
							<?php astra_markup_close( 'ast-comment-meta-wrap' ); ?>
							</header> <!-- .ast-comment-meta -->
							<div class="ast-comment-content comment">
							<?php comment_text(); 
								echo '</div><div class="ds62-comment-bottom-block">';
								if ( apply_filters( 'astra_single_post_comment_time_enabled', true ) ) {
									printf(
										esc_attr(
											astra_markup_open(
												'ast-comment-time',
												array(
													'open' => '<div %s>',
													'class' => 'ast-comment-time',
												)
											)
										) . '<span  class="timendate"><a style="pointer-events:none;" href="%1$s"><time datetime="%2$s">%3$s</time></a></span></div>',
										esc_url( get_comment_link( $comment->comment_ID ) ),
										esc_attr( get_comment_time( 'c' ) ),
										/* translators: 1: date, 2: time */
										esc_html( sprintf( __( '%1$s at %2$s', 'astra' ), get_comment_date(), get_comment_time() ) )
									);
								}
								comment_reply_link(
									array_merge(
										$args,
										array(
											'reply_text' => astra_default_strings( 'string-comment-reply-link', false ),
											'add_below' => 'comment',
											'depth'  => $depth,
											'max_depth' => $args['max_depth'],
											'before' => '<span class="ast-reply-link">',
											'after'  => '</span>',
										)
									)
								);
								echo '<div class="ast-comment-edit-reply-wrap">';
								edit_comment_link( astra_default_strings( 'string-comment-edit-link', false ), '<span class="ast-edit-link">', '</span>' );
								echo '</div></div>';
							?>
						</div>
						<?php if ( '0' == $comment->comment_approved ) : ?>
							<p class="ast-highlight-text comment-awaiting-moderation"><?php echo esc_html( astra_default_strings( 'string-comment-awaiting-moderation', false ) ); ?></p>
						<?php endif; ?>
						 <!-- .ast-comment-content -->
						<?php astra_markup_close( 'ast-comment-data-wrap' ); ?>
				</article><!-- #comment-## -->

			<?php
			break;
		}
}