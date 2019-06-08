### exportar shapefile para o mysql
```bash
ogr2ogr -f MySQL MySQL:qgis,host=localhost,user=root,password= C:\shape\15MUE250GC_SIR.shp -nln qgis -update -overwrite -lco engine=MYISAM
```