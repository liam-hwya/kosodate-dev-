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

            <div class="flex">
                <div>Article ID</div> 
                <div><?= $id ?></div>
            </div>
            
            <div class="flex">
                <div>
                    <p>Title</p>
                    <p>Link</p>
                    <p>Explanation</p>
                    <p>Contents</p>
                    <p>Image file associated with the article</p>
                    <p>Image file displayed in the article list</p>
                    <p>Related link 1</p>
                    <p>Related link 2</p>
                    <p>Related link 3</p>
                    <p>Delete modification flag</p>
                </div>
                <div>
                    <p><input type="text" value="value"></p>
                    <p><input type="text" value="value"></p>
                    <p><input type="text" value="value"></p>
                    <p><input type="text" value="value"></p>
                    <p><input type="text" value="value"></p>
                    <p><input type="text" value="value"></p>
                    <p><input type="text" value="value"></p>
                    <p><input type="text" value="value"></p>
                    <p><input type="text" value="value"></p>
                    <p><select name="delete" id="">
                        <option value="fix">Fix</option>
                        <option value="delete">Delete</option>
                    </select></p>
                    <input type="submit" value="Submit">
                </div>
            </div>
            

        </div>

        

    </div>
    </body>

</html>