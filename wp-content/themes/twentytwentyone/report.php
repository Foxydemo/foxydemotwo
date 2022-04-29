<?php /* Template Name: FoxyReport */ ?>

<?php get_header(); ?>


    
    
              
                    
                    
                                <?php
                             
                                 $args = array(  
                                'post_type' => 'Foxy_report_data',
                                'post_status' => 'publish',
                                'posts_per_page' => 10, 
                                'orderby' => 'title', 
                                'order' => 'ASC', 
                            );
                        
                            $loop = new WP_Query( $args ); 
                                 echo "<table>
                                 <th> ID </th>
                                 <th> First Name </th>
                                 <th> Last Name </th>
                                 <th> Email </th>
                                 <th> Date Created </th>
                                 <th> Current Date</th>
                                 <tr>";
                            while ( $loop->have_posts() ) : $loop->the_post(); 
                                // print the_title(); 
                                $cust_id = get_field('customer_id');
                                $fname= get_field('first_name');
                                $lname= get_field('last_name');
                                $email= get_field('email');
                                $date= get_field('date_created');
                                $cur_date = get_the_date('Y-m-d');
                                 echo "<td> $cust_id </td>
                                 <td> $fname </td>
                                 <td> $lname </td>
                                 <td> $email </td>
                                 <td> $date </td>
                                 <td> $cur_date </td>
                                 </tr>
                                 "; 
                                // the_excerpt(); 
                            endwhile;
                        
                            wp_reset_postdata(); 
                                ?>

<?php // get_footer(); ?>
             


                






