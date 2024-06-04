<table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">이름</th>
      <th scope="col">가격</th>
    </tr>
  </thead>
  <tbody>
    <?php $RCD = getDbSelect('legacy_products',"type='coffee'",'*');?>
    <?php $i=1;while($R = db_fetch_array($RCD)):?>
    <input type="hidden" name="uid[]" value="<?php echo $R['uid']?>" />
    <tr>
       <th scope="row"><?php echo $i?></th>
       <td><?php echo $R['name']?></td>
       <td><input name="price[]" value="<?php echo $R['price']?>" /></td>
    </tr>
   <?php $i++;endwhile?>
  </tbody>
</table>
<p>
  <button class="btn btn-primary pull-right" data-role="btn-save">저장</button>
</p>

<script> 
    
$('[data-role="btn-save"]').on('click',function(){
	var uid_arr = $('input[name="uid[]"]').map(function(){return $(this).val()}).get();
	var price_arr = $('input[name="price[]"]').map(function(){return $(this).val()}).get();
	console.log(price_arr);

    var data = {"uid_arr": JSON.stringify(uid_arr), "price_arr": JSON.stringify(price_arr)};

    $.post(rooturl+'/?r='+raccount+'&m=chatbot&a=save_legacyProduct',{
        data: data
    },function(response){
        var result=$.parseJSON(response);//$.parseJSON(response);
        location.reload();

    }); 
});    
</script>
