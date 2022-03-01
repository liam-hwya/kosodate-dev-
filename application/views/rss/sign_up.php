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
            border: 1px solid #000;
            padding: 20px;
        }

        .flex p a {
            text-decoration: none;
            color: #000;
        }

        .article-list {
            width: 60%;
            margin: 0 auto;
        }

        .article-list p {
            padding: 20px;
            border: 1px solid #000;
        }

        .article-list p a {
            color: #000;
            text-decoration: none;
        }

    </style>

<body>
    


    <div id="wrapper">


        <div class="container">
            <h3>RSS Registration screen</h3>

            <div class="flex">
                <div>Article Search</div> 
                <input type="text">
            </div>
            
            <div class="article-list">
                <p><a href="">Article</a></p>
                <p><a href="#">Article</a></p>
                <p><a href="#">Article</a></p>
                <p><a href="#">Article</a></p>
            </div>
            

        </div>

        

    </div>
    </body>

</html>