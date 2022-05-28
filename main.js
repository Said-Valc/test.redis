let key = null;
async function getItems(){
    let res = await fetch('http://php.practice.loc/redis/api/redis/');
    let data = await res.json();
    data = data.data;
    document.querySelector('.ul-list').innerHTML = '';
	for(item in data){
		 document.querySelector('.ul-list').innerHTML += `
				<li>${item}: ${data[item]} <a href='#' onclick="removeRedisItem('${item}')" class='remove'>delete</a></li>
        `
	}
}




async function removeRedisItem(key){
    const res = await fetch(`http://php.practice.loc/redis/api/redis/${key}`,{
        method: "DELETE"
    });

    const data = await res.json();
    if(data.status === true){
        await getItems();
    }
}



getItems();