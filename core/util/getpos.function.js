function getPos(elm) {
for(var zx=zy=0;elm!=null;zx+=elm.offsetLeft,zy+=elm.offsetTop,elm=elm.offsetParent);
return {x:zx,y:zy}
}

