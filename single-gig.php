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
    <?php 
    if ( have_posts() ) : while ( have_posts() ) : the_post();
      // Dynamic gig data from custom fields.
      $gig_short_description  = get_post_meta( get_the_ID(), 'gig_short_description', true );
      $gig_type               = get_post_meta( get_the_ID(), 'gig_type', true );
      $gig_payment_type       = get_post_meta( get_the_ID(), 'gig_payment_type', true );
      $gig_budget             = get_post_meta( get_the_ID(), 'gig_budget', true );
      $gig_full_description   = get_post_meta( get_the_ID(), 'gig_full_description', true );
      $gig_deadline           = get_post_meta( get_the_ID(), 'gig_deadline', true );
      $client_name            = get_the_author();

      // Get current user information.
      $current_user = wp_get_current_user();
      $gig_author_id = get_the_author_meta('ID');
      $allowed = false;
      // Allow viewing if logged in as admin, freelancer, or the gig's owner.
      if ( is_user_logged_in() ) {
          if ( current_user_can('administrator') ) {
              $allowed = true;
          } elseif ( in_array('freelancer', (array)$current_user->roles) ) {
              $allowed = true;
          } elseif ( $current_user->ID === (int)$gig_author_id ) {
              $allowed = true;
          }
      }

      // If not allowed, show a login prompt and exit.
      if ( ! $allowed ) {
          echo '<div class="container mx-auto px-4 py-8">';
          echo '<p>Login to see this gig</p>';
          wp_login_form();
          echo '</div>';
          return;
      }

      // Query for the number of bids associated with this gig.
      $bids_query = new \WP_Query( array(
          'post_type'      => 'bid',
          'meta_query'     => array(
              array(
                  'key'     => 'associated_gig',
                  'value'   => get_the_ID(),
                  'compare' => '='
              )
          ),
          'posts_per_page' => -1,
          'fields'         => 'ids',
      ) );
      $bids_count = $bids_query->found_posts;

      // Process bid submission if the form is submitted.
      if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['submit_bid'] ) ) {
          if ( !isset( $_POST['bid_nonce'] ) || !wp_verify_nonce( $_POST['bid_nonce'], 'submit_bid' ) ) {
              echo '<p class="text-red-600">Security check failed.</p>';
          } else {
              $bid_amount   = isset( $_POST['bidAmount'] ) ? sanitize_text_field( $_POST['bidAmount'] ) : '';
              $bid_comments = isset( $_POST['bidComments'] ) ? sanitize_textarea_field( $_POST['bidComments'] ) : '';
              $gig_id       = get_the_ID(); // current gig's ID

              // Create the Bid post.
              $bid_post = array(
                  'post_title'   => 'Bid for ' . get_the_title(),
                  'post_content' => '', // Leave empty since bid_note will hold the additional comments.
                  'post_status'  => 'publish', // Change to 'publish' if you want instant publication.
                  'post_author'  => $current_user->ID,
                  'post_type'    => 'bid'
              );
              $bid_id = wp_insert_post( $bid_post );
              if ( $bid_id ) {
                  // Associate the bid with the current gig via the ACF field.
                  update_post_meta( $bid_id, 'associated_gig', $gig_id );
                  // Map bid amount and bid note to their respective custom fields.
                  update_post_meta( $bid_id, 'bid_amount', $bid_amount );
                  update_post_meta( $bid_id, 'bid_note', $bid_comments );
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
        <div class="rounded-lg border border-solid border-slate-200 bg-card text-card-foreground shadow-sm">
          <div class="flex flex-col space-y-1.5 p-6">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-semibold tracking-tight text-2xl m-0"><?php echo esc_html( $gig_short_description ); ?></h3>
                <p class="text-muted-foreground mt-1 flex items-center m-0">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user w-4 h-4 mr-1">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  <?php echo esc_html( $client_name ); ?>
                </p>
              </div>
              <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-primary text-primary-foreground hover:bg-primary/80">
                <?php echo esc_html( $gig_type ); ?>
              </div>
            </div>
          </div>
          <div class="p-6 pt-0">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
              <!-- Budget -->
              <div class="flex items-center">
                
                <span class="font-semibold mr-2">Budget:</span>
                <?php
                  echo '$' . esc_html( $gig_budget ) . ' ' . esc_html( strtolower($gig_payment_type) );
                ?>
              </div>

              <!-- Deadline -->
              <div class="flex items-center">
                
                <span class="font-semibold mr-2">Deadline:</span>
                <?php 
                  if ( $gig_deadline ) {
                      echo date_i18n( get_option( 'date_format' ), strtotime( $gig_deadline ) );
                  } else {
                      echo 'N/A';
                  }
                ?>
              </div>

              <!-- Bids Submitted -->
              <div class="flex items-center">
                <span class="font-semibold mr-2">Bids Submitted:</span>
                <?php echo esc_html( $bids_count ); ?>
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

        <!-- Submit Bid Card (only for freelancers and admins) -->
        <?php if ( in_array( 'freelancer', (array)$current_user->roles ) || current_user_can('administrator') ) : ?>
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
        <?php endif; ?>
      </div>
    </div>
    <?php endwhile; endif; ?>
  </div>
</main>
<?php get_footer(); ?>
