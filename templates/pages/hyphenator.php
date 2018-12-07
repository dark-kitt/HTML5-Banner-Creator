<?php

get_header();

$serverROOT = $_SERVER['DOCUMENT_ROOT'];
require $serverROOT . 'app-classes/helper.php';

?>

<main class="hyphenator">

    <div class="head clearfix">

        <img id="delete-saved-value" src="<?= set_dir(); ?>app-assets/img/trash.svg" alt="">

        <span>Saved values:
            <select name="saved-values">
                <option value="">choose file</option>
                <?php
                    foreach (helper::custom_glob($serverROOT . 'app-assets/hyphenator-cache', '.json') as $file)
                    {
                        print '<option value="' . $file . '">' . basename($file, '.json') . '</option>';
                    }
                ?>
            </select>
        </span>

    </div>

    <form>
        <div class="source">
            <label>Input
                <textarea name="source" rows="8" placeholder="text.." required></textarea>
            </label>
        </div>

        <div class="options clearfix">
            <fieldset>
                <span>Replace in</span>

                <input type="radio" name="replace" value="html">
                <label for="html">html</label>

                <input type="radio" name="replace" value="js">
                <label for="js">js</label>

                <input type="radio" name="replace" value="input">
                <label for="input">input / self</label>


                <label>Hyphenchar
                    <input name="char" type="text" placeholder="default: |"/>
                </label>

                <label>Identifier
                    <input name="identifier" type="text" placeholder="id=&#34;id || class=&#34;class || tag OR js var => var test"/>
                </label>

                <label>Directory
                    <input name="directory" type="text" placeholder="/projects..<- without | => /path/to/your/project" required/>
                </label>

                <span>Language:
                    <select name="language" required>
                        <option value="">choose file</option>
                        <?php
                            foreach (helper::custom_glob($serverROOT . 'js/vendor/hyphenator-patterns', '.js') as $pattern)
                            {
                                print '<option value="' . basename($pattern, '.js') . '">' . basename($pattern, '.js') . '</option>';
                            }
                        ?>
                    </select>
                </span>
            </fieldset>
        </div>

        <div class="exeptions">
            <label>Exeptions
                <textarea name="exeptions" rows="2" placeholder="exeption, exeption, exeption.."></textarea>
            </label>
        </div>
    </form>

    <button class="button-light button-hyphenator">Hyphenate it!</button>

    <button id="save-values" class="button-light button-save-values">Save values</button>
</main>

<?php get_footer(); ?>
