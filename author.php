<?php

/**
 * The author template
 *
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
        <div class="container mx-auto px-4 py-8">
            <div class="space-y-6">
                <div class="rounded-lg border border-solid border-slate-200 bg-card text-card-foreground shadow-sm" data-v0-t="card">
                    <div class="p-6 pt-6">
                        <div class="flex flex-col md:flex-row items-center md:items-start md:space-x-6">
                        <?php 
                            $user_id = get_the_author_meta('ID'); 
                            $custom_avatar = get_user_meta($user_id, 'freelancer_profile_picture', true);

                            if (!empty($custom_avatar)) {
                                echo '<img src="' . esc_url($custom_avatar) . '" alt="' . esc_attr(get_the_author_meta('display_name')) . '" width="200" height="200" class="rounded-full">';
                            } else {
                                echo '<img src="https://via.placeholder.com/200" alt="Default Profile Picture" width="200" height="200" class="rounded-full">';
                            }
                        ?>

                            <div class="mt-4 md:mt-0 text-center md:text-left flex-grow">
                            <h1 class="text-3xl font-bold"><?php echo esc_html(get_the_author_meta('display_name')); ?></h1>

                                <div class="flex items-center justify-center md:justify-start mt-2 text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-4 h-4 mr-1">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg><span><?php echo esc_html(get_user_meta(get_the_author_meta('ID'), 'freelancer_location', true)); ?></span>
                                    </div>
                                    <?php if (get_user_meta(get_the_author_meta('ID'), 'freelancer_available', true)) : ?>
                                        <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-black text-white hover:bg-black/80 mt-2"
                                        data-v0-t="badge">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big w-4 h-4 mr-1">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <path d="m9 11 3 3L22 4"></path>
                                            </svg>
                                            Available for hire
                                        </div>
                                    <?php else : ?>
                                        <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-gray-300 text-gray-700 mt-2" data-v0-t="badge">
                                            Currently not taking on new clients
                                        </div>
                                    <?php endif; ?>

                                    <div class="mt-4 space-y-2">
                                        <div class="flex justify-between max-w-xs mx-auto md:mx-0">
                                            <span class="text-muted-foreground">Total Earned:</span>
                                            <span class="font-semibold">
                                                $<?php echo number_format((float) get_user_meta(get_the_author_meta('ID'), 'freelancer_earned', true), 2); ?>
                                            </span>
                                        </div>
                                        <div class="flex justify-between max-w-xs mx-auto md:mx-0">
                                            <span class="text-muted-foreground">Jobs Completed:</span>
                                            <span class="font-semibold">
                                                <?php echo esc_html(get_user_meta(get_the_author_meta('ID'), 'freelancer_jobs_completed', true) ?: 0); ?>
                                            </span>
                                        </div>
                                    </div>

                            </div>
                            <div class="mt-4 md:mt-0 md:ml-auto"><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none cursor-pointer disabled:opacity-50 [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail w-4 h-4 mr-2">
                                        <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                    </svg> Contact Freelancer</button></div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1 space-y-6">
                        <div class="rounded-lg border border-solid border-slate-200 bg-card text-card-foreground shadow-sm" data-v0-t="card">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <h3 class="text-2xl font-semibold leading-none tracking-tight">Languages</h3>
                            </div>
                            <div class="p-6 pt-0">
                            <div class="flex flex-wrap gap-2">
                                <?php
                                $languages = get_user_meta(get_the_author_meta('ID'), 'freelancer_languages', true);
                                
                                if (!empty($languages) && is_array($languages)) {
                                    foreach ($languages as $language) {
                                        echo '<div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80" data-v0-t="badge">'
                                            . esc_html($language) .
                                            '</div>';
                                    }
                                } else {
                                    echo '<div class="text-muted-foreground">No languages listed</div>';
                                }
                                ?>
                            </div>

                            </div>
                        </div>
                        <div class="rounded-lg border border-solid border-slate-200 bg-card text-card-foreground shadow-sm" data-v0-t="card">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <h3 class="text-2xl font-semibold leading-none tracking-tight">Skills</h3>
                            </div>
                            <div class="p-6 pt-0">
                                <div class="flex flex-wrap gap-2">
                                    <?php
                                    $skills = get_user_meta(get_the_author_meta('ID'), 'freelancer_skills', true);

                                    if (!empty($skills) && is_array($skills)) {
                                        foreach ($skills as $skill) {
                                            echo '<div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 text-foreground" data-v0-t="badge">'
                                                . esc_html($skill) .
                                                '</div>';
                                        }
                                    } else {
                                        echo '<div class="text-muted-foreground">No skills listed</div>';
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="rounded-lg border border-solid border-slate-200 bg-card text-card-foreground shadow-sm" data-v0-t="card">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <h3 class="text-2xl font-semibold leading-none tracking-tight">Work History</h3>
                            </div>
                            <div class="p-6 pt-0">
                                <div class="space-y-6">
                                    <div class="border-b last:border-b-0 pb-4 last:pb-0">
                                        <h3 class="text-lg font-semibold">E-commerce Website Redesign</h3>
                                        <div class="flex justify-between text-sm text-muted-foreground mt-1"><span>Completed: 5/14/2023</span><span>Cost: $3,500</span></div>
                                        <p class="mt-2 text-sm italic">"Excellent work! Jane delivered the project on time and exceeded our expectations."</p>
                                    </div>
                                    <div class="border-b last:border-b-0 pb-4 last:pb-0">
                                        <h3 class="text-lg font-semibold">Mobile App Development</h3>
                                        <div class="flex justify-between text-sm text-muted-foreground mt-1"><span>Completed: 2/28/2023</span><span>Cost: $5,000</span></div>
                                        <p class="mt-2 text-sm italic">"Great communication and high-quality code. Would definitely hire again."</p>
                                    </div>
                                    <div class="border-b last:border-b-0 pb-4 last:pb-0">
                                        <h3 class="text-lg font-semibold">Brand Identity Design</h3>
                                        <div class="flex justify-between text-sm text-muted-foreground mt-1"><span>Completed: 1/9/2023</span><span>Cost: $2,500</span></div>
                                        <p class="mt-2 text-sm italic">"Jane is a creative genius. Our new brand looks amazing!"</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main><!-- #primary -->
<?php

get_footer();
