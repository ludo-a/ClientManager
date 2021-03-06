<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/favicon.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Document</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body>
    <div id="app">
      <div id="divheader">
        <div class="divtitle">
          <img class="clientlogo" alt="Client Logo" src="./assets/client.png">
          <h1>Client Manager</h1>
        </div>
        <form id="myForm">
          <h3>Nouveau Client</h3>
          <label for="name">Nom :</label>
          <input class="inputfloat" type="text" v-model="form_name"></br>
          <label for="surname">Prénom :</label>
          <input class="inputfloat" type="text" v-model="form_surname"></br>
          <label for="subscribe">Abonnement :</label>
          <input class="inputfloat" type="number" v-model="form_subscribe"></br>
          <div class="buttongroup">
            <input class="btn btn-outline-secondary" type="button" @click="resetForm" value="Reset">  
            <button class="btn btn-primary" type="button" @click="pushData()">Ajouter</button>
          </div>
        </form>
      </div>
      <div id="searchbutton">
        <h4>Rechercher un client:</h4><input class="searchNameInput" type="text" v-model="form_search" @keyup="searchData" placeholder="Nom ou Prénom">
      </div>
      <table>
        <thead>
          <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Abonnement</th>
            <th>Box's Restantes</th>
            <th> <!-- laisser libre pour les boutons --> </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in allData">
			      <td>{{row.name}}</td>
            <td>{{row.surname}}</td>
            <td>{{row.subscribe}}</td>
            <td>{{row.boxnumber}}</td>
            <td>
              <button class="btn btn-primary" type='button' data-toggle="modal" data-target="#myModal" @click='updateData(row.id, row.boxnumber)'><i class="fas fa-folder-open"></i></button>
              <button class="btn btn-danger" type='button' @click='deleteData(row.id)'><i class="fas fa-trash"></i></button>
            </td>
          <tr v-if="nodata">
						<td>No Data Found</td>
					</tr>
          </tr>
        </tbody>
      </table>
      <div v-if="myModel">
        <div class="container">  
        <!-- The Modal -->
          <div class="modal fade" id="myModal">
            <div class="modal-dialog">
              <div class="modal-content">
              <!-- Modal Header -->
                <div class="modal-header">
                  <h4 class="modal-title">Editer le Profil</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                  <label>Ancien nombre de box: {{ boxnumber }}</label></br>
                  <label>NOUVEAU nombre de box</label>
                  <input type="number" class="form-control" v-model="newNumber"/>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                  <input type="button" class="btn btn-primary btn-xs" data-dismiss="modal" @click="submitData" value="Enregistrer"/>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>  
</body>
</html>

<script>

var liste = new Vue({
  el:'#app',
  data:{
    allData:'',
    form_name:'',
    form_surname:'',
    form_subscribe:'',
    form_boxnumber:'',
    form_search:'',
    nodata:false,
    myModel: false,
    boxnumber:'',
    hiddenId:'',
    newNumber:'',
  },
  methods:{
    fetchAllData:function(){
      axios.post('action.php', {
        action:'fetchall'
      }).then(function(response){
        liste.allData = response.data;
      });
    },
    pushData:function(){
      if(liste.form_name != '' && liste.form_surname != '' && liste.form_subscribe != ''){
        liste.form_boxnumber = liste.form_subscribe;
        axios.post('action.php', {
          action:'insert',
          name:liste.form_name,
          surname:liste.form_surname,
          subscribe:liste.form_subscribe,
          boxnumber:liste.form_boxnumber
        }).then(function(response){
          liste.fetchAllData();
          liste.form_name = '';
          liste.form_surname = '';
          liste.form_subscribe = '';
          liste.form_boxnumber = '';
          alert(response.data.message);
          liste.resetForm();
        })
      } else {
        alert('ERREUR : Une ou des données manquantes.');
      }
    },
    resetForm:function(){
      document.getElementById("myForm").reset();
    },
    deleteData:function(id){
      if(confirm("Êtes-vous sûr de vouloir supprimer le client?"))
      {
        axios.post('action.php', {
          action: 'delete',
          id:id
        }).then(function(response){
          liste.fetchAllData();
          alert(response.data.message);
        });
      }
    },
    searchData:function(){
      axios.post('action.php', {
        action: 'search',
        query:this.form_search
      }).then(function(response){
        if(response.data.length > 0)
        {
          liste.allData = response.data;
          liste.nodata = false;
        }
        else
        {
          liste.allData = '';
          liste.nodata = true;
        }
      })
    },
    updateData:function(id,boxnumber){
      liste.boxnumber = boxnumber;
      liste.hiddenId = id;
      liste.myModel = true;
    },
    submitData:function(){
      if(liste.newNumber != ''){
        axios.post('action.php', {
        action:'update',
        boxnumber:liste.newNumber,
        hiddenId:liste.hiddenId
      }).then(function(response){
        liste.myModel = false;
        liste.fetchAllData();
        liste.newNumber = '';
        liste.hiddenId = '';
        liste.boxnumber = '';
        alert(response.data.message);
      });
      } else{
        alert("ERREUR : Veuillez indiquer le nombre de box's restantes.")
      }
    }
  },
  created:function(){
    this.fetchAllData();
  }
});

</script>