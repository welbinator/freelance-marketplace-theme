<?php
/**
 * The gig template 
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header();

wp_rig()->print_styles('wp-rig-content');
?>
<main id="primary" class="site-main">
  <div class="contents">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
      // Dynamic gig data from custom fields.
      $gig_short_description  = get_post_meta( get_the_ID(), 'gig_short_description', true );
      $gig_type               = get_post_meta( get_the_ID(), 'gig_type', true );
      $gig_payment_type       = get_post_meta( get_the_ID(), 'gig_payment_type', true );
      $gig_budget             = get_post_meta( get_the_ID(), 'gig_budget', true );
      $gig_full_description   = get_post_meta( get_the_ID(), 'gig_full_description', true );
      $gig_deadline           = get_post_meta( get_the_ID(), 'gig_deadline', true );
      $client_name            = get_the_author();

      // Process bid submission if the form is submitted.
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_bid'])) {
          if (!isset($_POST['bid_nonce']) || !wp_verify_nonce($_POST['bid_nonce'], 'submit_bid')) {
              echo '<p class="text-red-600">Security check failed.</p>';
          } else {
              $bid_amount   = isset($_POST['bidAmount']) ? sanitize_text_field($_POST['bidAmount']) : '';
              $bid_comments = isset($_POST['bidComments']) ? sanitize_textarea_field($_POST['bidComments']) : '';
              $current_user = wp_get_current_user();
              $gig_id       = get_the_ID(); // current gig's ID

              // Create the Bid post.
              $bid_post = array(
                  'post_title'   => 'Bid for ' . get_the_title(),
                  'post_content' => '', // Leave empty since bid_note will hold the additional comments.
                  'post_status'  => 'pending', // Change to 'publish' if you want instant publication.
                  'post_author'  => $current_user->ID,
                  'post_type'    => 'bid'
              );
              $bid_id = wp_insert_post($bid_post);
              if ($bid_id) {
                  // Associate the bid with the current gig via the ACF field.
                  update_post_meta($bid_id, 'associated_gig', $gig_id);
                  // Map bid amount and bid note to their respective custom fields.
                  update_post_meta($bid_id, 'bid_amount', $bid_amount);
                  update_post_meta($bid_id, 'bid_note', $bid_comments);
                  echo '<p class="text-green-600">Your bid has been submitted!</p>';
              } else {
                  echo '<p class="text-red-600">There was an error submitting your bid. Please try again.</p>';
              }
          }
      }
    ?>
    <div class="container mx-auto px-4 py-8">
      <div class="space-y-6">
        <!-- Gig Overview Card -->
        <div class="rounded-lg border border-solid border-slate-200 bg-card text-card-foreground shadow-sm" data-v0-t="card">
          <div class="flex flex-col space-y-1.5 p-6">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-semibold tracking-tight text-2xl m-0"><?php the_title(); ?></h3>
                <p class="text-muted-foreground mt-1 flex items-center m-0">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user w-4 h-4 mr-1">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  <?php echo esc_html( $client_name ); ?>
                </p>
              </div>
              <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-primary text-primary-foreground hover:bg-primary/80" data-v0-t="badge">
                <?php echo esc_html( $gig_type ); ?>
              </div>
            </div>
          </div>
          <div class="p-6 pt-0">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
              <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dollar-sign w-4 h-4 mr-1">
                  <line x1="12" x2="12" y1="2" y2="22"></line>
                  <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                <span class="font-semibold mr-2">Budget:</span>
                <?php
                  if ( strcasecmp( $gig_payment_type, 'Project fee' ) === 0 ) {
                      echo '$' . number_format( floatval( $gig_budget ) );
                  } else {
                      echo '$' . esc_html( $gig_budget ) . '/hour';
                  }
                ?>
              </div>
              <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days w-4 h-4 mr-1">
                  <path d="M8 2v4"></path>
                  <path d="M16 2v4"></path>
                  <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                  <path d="M3 10h18"></path>
                  <path d="M8 14h.01"></path>
                  <path d="M12 14h.01"></path>
                  <path d="M16 14h.01"></path>
                  <path d="M8 18h.01"></path>
                  <path d="M12 18h.01"></path>
                  <path d="M16 18h.01"></path>
                </svg>
                <span class="font-semibold mr-2">Deadline:</span>
                <?php 
                  if ( $gig_deadline ) {
                      echo date_i18n( get_option( 'date_format' ), strtotime( $gig_deadline ) );
                  } else {
                      echo 'N/A';
                  }
                ?>
              </div>
              <div class="flex items-center">
                <span class="font-semibold mr-2">Payment Type:</span>
                <?php echo esc_html( $gig_payment_type ); ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Detailed Description Card -->
        <div class="rounded-lg border border-solid border-slate-200 bg-card text-card-foreground shadow-sm" data-v0-t="card">
          <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-2xl font-semibold leading-none tracking-tight m-0">Detailed Description</h3>
          </div>
          <div class="p-6 pt-0">
            <p class="whitespace-pre-wrap m-0"><?php echo esc_html( $gig_full_description ); ?></p>
          </div>
        </div>

        <!-- Submit Bid Card -->
        <div class="rounded-lg border border-solid border-slate-200 bg-card text-card-foreground shadow-sm" data-v0-t="card">
          <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-2xl font-semibold leading-none tracking-tight m-0">Submit Your Bid</h3>
          </div>
          <div class="p-6 pt-0">
            <form method="post" class="space-y-4">
              <div>
                <label for="bidAmount" class="block text-sm font-medium text-gray-700 mb-1">Your Bid Amount ($)</label>
                <input id="bidAmount" name="bidAmount" type="number" placeholder="Enter your bid amount" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" />
              </div>
              <div>
                <label for="bidComments" class="block text-sm font-medium text-gray-700 mb-1">Additional Comments</label>
                <textarea id="bidComments" name="bidComments" placeholder="Enter any additional comments or questions about the gig" rows="4" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"></textarea>
              </div>
              <button type="submit" name="submit_bid" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                Submit Bid
              </button>
              <?php wp_nonce_field( 'submit_bid', 'bid_nonce' ); ?>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; endif; ?>
  </div>
</main>
<?php get_footer(); ?>
