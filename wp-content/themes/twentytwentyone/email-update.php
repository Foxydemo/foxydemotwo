<?php 

/* Template Name: Email Update Page */
get_header();
global $wpdb;

if ($_POST) {

    $args = array(
        'ID'         => 4,
        'user_pass' => esc_attr($_POST['password']),
        'user_email' => esc_attr($_POST['email'])
    );

    wp_update_user($args);

    $wpdb->update(
        $wpdb->users, 
        ['user_login' => esc_attr($_POST['email'])],
        ['ID' => 4]
    );
    
    echo "Email updated Successfully";
    exit();
}
?>

<form method="post">
    <table border="0" align="center">
        <tbody>

            <tr>
                <td><label for="email">Email_Address:</label></td>
                <td><input id="email" maxlength="50" name="email" type="text" /></td>
            </tr>

            <tr>
                <td><label for="password">Password:</label></td>
                <td><input id="password" maxlength="50" name="password" type="password" /></td>
            </tr>

            <tr>
                <td align="right"><input name="Submit" type="Submit" value="Submit" /></td>
            </tr>

        </tbody>
    </table>
</form>

<?php get_footer(); ?>