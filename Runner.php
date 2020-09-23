<?php
if (isset($_FILES['keywordsFile'])) {

    $apiKey = $_POST['apikey'];
    $cseCx = $_POST['cse_cx'];
    $domains = $_POST['domains'];
    $keywordsCsv = $_FILES['keywordsFile']['tmp_name'];

    $uniqName = uniqid('upload_', TRUE);
    $keywordsFile = "./tmp/keywords_$uniqName.csv";
    $domainsFile = "./tmp/domains_$uniqName.txt";

    move_uploaded_file($keywordsCsv, $keywordsFile);

    file_put_contents($domainsFile, $domains);

    // shell_exec("echo php -q src/Serp.php $apiKey $cseCx $uniqName | at now 2>&1");
    shell_exec("php src/Serp.php $apiKey $cseCx $uniqName > /dev/null 2>/dev/null &");
}
?>
<form method="post" enctype="multipart/form-data">
    <input type="text" placeholder="API Key" name="apikey" id="apikey" />
    <input type="text" placeholder="CSE CX" name="cse_cx" id="cse_cx" />
    <textarea name="domains" id="domains" placeholder="Domains"></textarea>
    <input type="file" name="keywordsFile" id="kewordsFile">
    <input type="submit" value="Process" name="submit">
</form>
<?php
    if (isset($_FILES['keywordsFile'])) :
?>
<div id="progress">Please Wait While Processing</div>
<script>
var $elem = document.getElementById("progress");
var source = new EventSource("MessageReader.php");
source.onmessage = function(e) {
    console.log(e);
    msg = JSON.parse(e.data);
    progress = msg['progress'];
    if(msg['error'] !== undefined){
        source.close();
        $elem.innerHTML = '';
        $elem.innerText = msg['error'];
    }else if(progress < 100){
        $elem.innerHTML = '';
        $elem.innerText = `Please Wait While Processing ${progress}%`;
    }else if(progress == 100){
        link = msg['link'];
        if(link !== undefined){
            source.close();
            $elem.innerText = '';
            $elem.innerHTML = `Your Download Should Start Shortly, if not <a href="${link}">Click here</a>`;
            window.location = link;
        }
        // console.log(link);
    }
}
</script>
<?php
    endif;