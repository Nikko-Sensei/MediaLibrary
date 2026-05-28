<?php require BASE_PATH . '/view/Layout/header.php'; ?>

<div class="section page">
    <div class="wrapper">

        <h1>Suggest a median items</h1>
        <br><br>
        <?php
        if (isset($_GET["status"]) && $_GET["status"] == "thanks") 
        {
            echo "<p>Thanks for the email! I&rsquo;ll check out your suggestion shortly!</p>";
        } else {

            if (isset($error_message)) {
                echo '<p class="message">' . htmlspecialchars($error_message) . '</p>';
            } else {
                echo '
                    <p>If you think there is something I&rsquo;m missing, let me know!</p>
                    <p>Complete the form to send an email.</p>
                ';
            }
        ?>
            <form method="post" class="suggest-form" action="">

                <div class="form-group">
                    <label for="name">Name (required)</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?php if (isset($name)) echo $name; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email (required)</label>
                    <input
                        type="text"
                        id="email"
                        name="email"
                        value="<?php if (isset($email)) echo $email; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="category">Category (required)</label>
                    <select id="category" name="category">
                        <option value="" disabled selected>Select One</option>

                        <?php
                        foreach ($categories as $cat) {
                            echo "<option value=\"" . htmlspecialchars($cat) . "\"";

                            if (isset($category) && $category === $cat) {
                                echo " selected";
                            }

                            echo ">" . htmlspecialchars($cat) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Title (required)</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?php if (isset($title)) echo $title; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="format">Format</label>
                    <select id="format" name="format">
                        <option value="" disabled selected>Select One</option>

                        <?php
                        foreach ($formats as $category => $optgroups) {
                            echo "<optgroup label=\"" . htmlspecialchars($category) . "\">";

                            foreach ($optgroups as $optgroup) {
                                echo "<option value=\"" . htmlspecialchars($optgroup) . "\"";

                                if (isset($format) && $format == $optgroup) {
                                    echo " selected";
                                }

                                echo ">" . htmlspecialchars($optgroup) . "</option>";
                            }

                            echo "</optgroup>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="genre">Genre</label>
                    <select id="genre" name="genre">
                        <option value="" disabled selected>Select One</option>

                        <?php
                        foreach ($genres as $category => $options) {
                            echo "<optgroup label=\"$category\">";

                            foreach ($options as $option) {
                                echo "<option value=\"$option\"";

                                if (isset($genre) && $genre == $option) {
                                    echo " selected";
                                }

                                echo ">$option</option>";
                            }

                            echo "</optgroup>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="year">Year</label>
                    <input
                        type="text"
                        id="year"
                        name="year"
                        value="<?php if (isset($year)) echo $year; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="details">Additional Details</label>
                    <textarea name="details" id="details"><?php
                        if (isset($details)) echo htmlspecialchars($_POST['details']);
                    ?></textarea>
                </div>

                <div class="form-group" style="display:none">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address">
                    <p>Please leave this field blank</p>
                </div>
                <input type="submit" value="Send" class="btn">

            </form>
        <?php } ?>

    </div>
</div>

<?php require BASE_PATH . '/view/Layout/footer.php'; ?>
