
<html>
    <style>
        .container {
            width: 60%;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 20px;
        }

        h3 {
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }

        .flex {
            display: flex;
            width: 60%;
            margin: 0 auto;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .flex > div:first-child {width: 40%;}

        .flex > div:last-child {
            width: 60%;
        }

        .flex p {
            padding: 20px;
        }

        .flex p a {
            text-decoration: none;
            color: #000;
        }

    </style>

<body>
    


    <div id="wrapper">


        <div class="container">
            <h3>RSS registration screen</h3>

            <?php if(is_null($manga_detail)): ?>

            <p>There is no manga related with this manga id <b><?= $manga_id ?></b></p>

            <?php else: ?>

                <?php if(isset($action) && $action=='register'): ?>
               
                    <div class="flex">
                        <div>Article ID</div> 
                        <div><?= $manga_detail->manga_id ?></div>
                    </div>
                
                    <div class="flex">
                        <p>TItle</p>
                        <p><b><?= $manga_detail->manga_title ?></b></p>
                    </div>

                    <div class="flex">
                        <p>Link</p>
                        <p><b><?= MANGA_URL.$manga_detail->manga_id ?></b></p>
                    </div>

                    <div class="flex">
                        <p>Explanation</p>
                        <p><b><?= $manga_detail->manga_intro ?></b></p>
                    </div>

                    <div class="flex">
                        <p>Contents</p>
                        <p><b><?= $manga_detail->manga_detail ?></b></p>
                    </div>

                    <div class="flex">
                        <p>Image file associated with the article</p>
                        <?php if(!is_null($manga_media)): ?>
                            <p>
                                <b><?= (empty($media['img_url']))? '' : KOSODATE_IMG_URL.$manga_media[0]['img_url'] ?></b>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="flex">
                        <p>Image file displayed in the article list</p>
                        <?php if(!is_null($manga_media)): ?>
                            <p>
                                <?php foreach($manga_media as $media): ?>
                                    <b><?= (empty($media['img_url']))? '' : KOSODATE_IMG_URL.$media['img_url'] ?></b>
                                <?php endforeach; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if(!is_null($related_manga)): ?>
                        <?php foreach($related_manga as $key=>$manga): ?>
                        <div class="flex">
                            <p>Related link <?= $key+1 ?></p>
                            <p>
                                <b><?= MANGA_URL.$manga['manga_id'] ?></b>
                            </p>            
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <form action="<?= $register_url ?>" method="POST">
                        <input type="hidden" name="manga_id" value="<?= $manga_id ?>">
                        <input type="submit" name="manga_register" value="register">
                    </form>
                
                <?php elseif(isset($action) && $action=='update'): ?>

                <form action="<?= $update_url ?>" method="POST">

                    <div class="flex">
                        <div>Article ID</div> 
                        <input type="hidden" name="manga[guid]" value="<?= $manga_detail[0]['guid'] ?>">
                    </div>
                
                    <div class="flex">
                        <p>TItle</p>
                        <input type="text" name="manga[title]" value="<?= $manga_detail[0]['title'] ?>">
                    </div>

                    <div class="flex">
                        <p>Link</p>
                        <input type="text" name="manga[link]" value="<?= MANGA_URL.$manga_detail[0]['guid'] ?>">
                    </div>

                    <div class="flex">
                        <p>Explanation</p>
                        <textarea name="manga[description]" id="" cols="30" rows="10">
                            <?= $manga_detail[0]['description'] ?>
                        </textarea>
                    </div>

                    <div class="flex">
                        <p>Contents</p>
                        <textarea name="manga[encoded]" id="" cols="30" rows="10">
                            <?= $manga_detail[0]['encoded'] ?>
                        </textarea>
                    </div>

                    <div class="flex">
                        <p>Image file associated with the article</p>
                        <?php if(!is_null($manga_media)): ?>
                            <p>
                                <input type="text" name="manga[img_url][thumbnail]" value="<?= KOSODATE_IMG_URL.$manga_media[0]['img_url'] ?>">
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="flex">
                        <p>Image file displayed in the article list</p>
                        <?php if(!is_null($manga_media)): ?>
                            <p>
                                <?php foreach($manga_media as $media): ?>
                                    <input type="text" name="manga[img_url][enclosure][]" value="<?= KOSODATE_IMG_URL.$media['img_url'] ?>">
                                <?php endforeach; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if(!is_null($related_manga)): ?>
                        <?php foreach($related_manga as $key=>$manga): ?>
                            <div class="flex">
                                <p>Related link <?= $key+1 ?></p>
                                <p>
                                    <input type="text" name="manga[related_link][]" value="<?= MANGA_URL.$manga['manga_id'] ?>">
                                </p>            
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="flex">
                        <p>Delete</p>
                        <p>
                            <select name="manga[delete]" id="">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </p>
                    </div>

                    <?php 

                        if(!is_null($related_manga)):
                            foreach($related_manga as $key=>$manga):
                    ?>
                            <input type="hidden" name="related_manga[<?= $key ?>][manga_id]" value="<?= $manga['manga_id'] ?>" />
                            <input type="hidden" name="related_manga[<?= $key ?>][manga_title]" value="<?= $manga['manga_title'] ?>" />
                            <input type="hidden" name="related_manga[<?= $key ?>][img_url]" value="<?= $manga['img_url'] ?>" />
                    <?php
                            endforeach;
                        endif;
                    ?>

                    <!-- No edited manga detail information -->
                    <input type="hidden" name="manga[category]" value="<?= $manga_detail[0]['category'] ?>" />
                    <input type="hidden" name="manga[pubDate]" value="<?= $manga_detail[0]['pubDate'] ?>" />
                    
                    <input type="submit" name="manga_update" value="update">

                    </form>

                <?php endif; ?>

            <?php endif; ?>

        </div>

        

    </div>
    </body>

</html>