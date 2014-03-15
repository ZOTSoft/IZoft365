// JavaScript Document
	var selectedrow=0;
	var selecdetcell;
	var simplemenu;
	var rowcount=1;
	var curpos=0;



function createrMenu(parentId){
	var menudiv = document.getElementById("menucell");
	var newBtn;
	if (parentId>0){
		var newBtn = document.createElement("button");
		newBtn.setAttribute("class","menubtns");
		newBtn.setAttribute("id",0);
		newBtn.setAttribute("onClick",'menuBtnClick(this)');
		newBtn.innerHTML="Все";
		menudiv.appendChild(newBtn);
		while (simplemenu[curpos].parentId>0){
			
		}
	}
}

function showSimpleMenu(){
	var menudiv = document.getElementById("menucell");
	//for tests
	var names = new Array('Категория 1','Категория 2','Категория 3','Картошка','Пиченька','Конфетка','Сок');
	var id = new Array(1,2,3,4,5,6,7);
	var prices = new Array(250,350,888,450);  
	var parentid = new Array(0,0,0,0,0,0,0);
	var isgroup = new Array(0,0,0,1,1,1,1);
	simplemenu = new Array();
	curpos=0;
	for (i=0;i<names.length;i++){
		simplemenu[i] = new Object();
		simplemenu[i].name=names[i];
		simplemenu[i].id=id[i];
		simplemenu[i].price=prices[i];
		simplemenu[i].pos=curpos;
		simplemenu[i].parentId=parentid[i];
		curpos++;
	}
	/*
	for (j=0;j<simplemenu.length;j++){
		var newBtn = document.createElement("button");
		newBtn.setAttribute("class","menubtns");
		newBtn.setAttribute("id",j);
		newBtn.setAttribute("onClick",'menuBtnClick(this)');
		newBtn.innerHTML=simplemenu[j].name;
		menudiv.appendChild(newBtn);
	}
	*/
}

function menuBtnClick(btn){
	var headers = new Array('c','c','c','c','c','c','c');
	var values = new Array(7);
	var count=1;
	var idMenu = btn.getAttribute("id");
	values[0]=rowcount;
	values[1]='0';
	values[2]=simplemenu[idMenu].name;
	values[3]=simplemenu[idMenu].price;
	values[4]=count;
	values[5]=count*simplemenu[idMenu].price;
	values[6]=simplemenu.id;
	addRow(headers,values);
	rowcount++;
}


function addRow(headers,values){
	//Подключение к таблице3.
	var ordertable = document.getElementById("ordertable");
	//Создание строки к таблице
	var newRow = document.createElement("tr");
	newRow.setAttribute("onClick","rowClick(this)");
	newRow.setAttribute("id",rowcount+"r")
	newRow.setAttribute("class","rowbackground");
	//Создание ячеек таблицы
	for (i=0;i<headers.length-1;i++){
		var newCell = document.createElement("td");
		newCell.setAttribute("class","orderrow");
		newCell.setAttribute("id",i+headers[i]+rowcount+"r");
		newCell.innerHTML=values[i];
		newRow.appendChild(newCell);	
	}
	//Создание элемента таблици с id товара
	var newCell = document.createElement("td");
	newCell.setAttribute("class","orderidcell");
	newCell.setAttribute("id",i+headers[i]+rowcount+"r");	
	newCell.innerHTML=values[6];
	newRow.appendChild(newCell);
	//Конец создания ячеек таблицы
	ordertable.appendChild(newRow);
}

function rowClick(cell){
	var ordertable = document.getElementById("ordertable");
	row =cell.sectionRowIndex;
	selectedrow=cell.sectionRowIndex;
	//Selectedrow
	for (i=1;i<=rowcount-1;i++){
			var cells = document.getElementById(i+"r");
			cells.removeAttribute("style");
	}
	selectedcell = document.getElementById((row+1)+"r");
	selectedcell.setAttribute("style","background-color:#778899")
	//selectedcell.style.backgroundColor='#778899';
	//End

}

function deleteRow(){
	//Removerow
	var ordertable = document.getElementById("ordertable");
	ordertable.deleteRow(selectedrow+1);
	rowcount--;
	for (i=selectedrow+1;i<=rowcount-1;i++){
	   firstcell = document.getElementById("0c"+(i+1)+"r");	
	   firstcell.setAttribute("id","0c"+(i)+"r");
	   firstcell.innerHTML=i;
	   for (j=1;j<=5;j++){
		   secondcell = document.getElementById(j+"c"+(i+1)+"r");
		   secondcell.setAttribute("id",j+"c"+(i)+"r");
	   }
	   selrow = document.getElementById((i+1)+"r");
	   selrow.setAttribute("id",(i)+"r");
	}
	//Remove	
}