<h2 class="title"><?php echo $page_name; ?></h2>

<section class="stretch-container" data-title="インスタ登録画面">
    <form action="ad_instagram/register" method="POST">
        <dl class="form-item">
            <dt>インスタ埋め込みタグ文字</dt>
            <dd>
                <textarea name="tag" id="" cols="30" rows="10"></textarea>
            </dd>
        </dl>

        <input type="submit" value="add" name="add">
    </form>
    
</section>

<?php

 if(isset($message)) :

    ?>

    <section class="my-table">
        <div class="tbody">
            <div class="tr">
                <div class="td"><?php $message ?></div>
            </div>
        </div>
    </section>

    <?php

 endif;

?>

