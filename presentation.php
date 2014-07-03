<!DOCTYPE HTML>
<html>
	<head>
        <title>Personnalisation et MOOCs</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link href="css/bootstrap.css" type="text/css" rel="stylesheet"/>
		<link href="css/main.css" type="text/css" rel="stylesheet"/>
		
		
	</head>
    
    <body>
        <div class="container presentation">
            <h1>Mise en place de la personnalisation dans le cadre des MOOCs</h1>
            <p><a href="index.php" id="mainLink">Retour au menu principal</a></p>
            <ul>
                <li><a href="#model">Présentation du modèle</a>
                <li><a href="#appli">Présentation de l'application</a>
                  
            </ul>
            
            <p>Pour une compréhension rapide des enjeux qui se trouvent derrière la mise en place de la personnalisation dans le cadre des MOOCs, vous êtes invités à consulter la page de <a class='toTranslate' href="http://liris.cnrs.fr/coatcnrs/wiki/doku.php?id=florian_clerc_stage_master_recherche_et_ingenieur_31_mars-_26_septembre">présentation du stage recherche concerné par le projet</a>, si vous ne l'avez pas déjà fait. Une présentation au format pdf est également disponible sur cette page, voici le <a class='toTranslate' href="http://liris.cnrs.fr/coatcnrs/wiki/lib/exe/fetch.php?media=presentation_florian_clerc_20140523_silex.pdf">lien direct</a>.</p>
            
            
            <h2 id="model">Présentation du modèle</h2>
            <p>Voici tout d'abord le cycle global dans lequel s'inscrit ce projet, au sein du projet COAT :</p>
            <img src="img/cyclePersonnalisation.PNG" alt="Cycle de Personnalisation des activités"/>
            <p>Le principe est simple : au sein d'un MOOC, les apprenants vont réaliser des activités, au cours desquelles toutes leurs actions vont pouvoir être tracées. Grâce aux traces générées par ces interactions avec la plateforme, un profil d'apprenant va pouvoir être généré pour chacun. L'enseignant en charge du MOOC va de son côté définir une stratégie pédagogique. Cette stratégie pédagogique va permettre de déterminer de manière automatique pour chacun des apprenants de nouvelles activités et de nouveaux parcours, en fonction des informations contenues dans son profil. Puis, le cycle va pouvoir recommencer, puisque de nouvelles traces vont être générées par les apprenants lorsqu'ils réaliseront ces activités.</p>
            <p>Dans ce projet, nous nous intéressons particulièrement au processus de définition de sa stratégie pédagogique par l'enseignant, afin de lui permettre d'exprimer au mieux les objectifs pédagogiques qu'il a au cours du MOOC.</p>
            
            
            
            <p>A partir du modèle PERSUA2<sup><a href="#footnotePersua2">1</a></sup>, une adaptation pour les MOOCs a été réalisée afin d'exploiter du mieux possible les perspectives offertes par les plateformes. Voici le processus d'exploitation qui est associé, et qui permet de comprendre la façon dont fonctionnera, à terme, la présente application : </p>
            <img src="img/processusPersua2mooc.PNG" alt="Processus d'exploitation de PERSUA2mooc"/>
            <p>Voici une explication sommaire, pour plus de détails sur chacun des éléments, se reporter à la section suivante : </p>
            <p>En entrée du processus se trouvent quatre éléments. Deux d'entre eux vont permettre de caractériser l'apprenant, et sont calculés de manière automatique : le profil, et le contexte d'utilisation 'live'. Le profil contient des indicateurs qui permettent de décrire qui est l'apprenant ainsi que la manière dont il a interagi avec le MOOC depuis le début de celui-ci. Le contexte d'utilisation 'live' contient quant à lui des informations qui vont permettre de caractériser la situation à un instant donnée, lorsque l'apprenant se connecte à la plateforme : l'heure précise, l'appareil avec lequel l'apprenant se connecte, la bande passante dont il dispose,...ainsi que des informations sur l'environnement du MOOC en général, comme par exemple le nombre d'apprenants connectés.</p>
            <p>Les deux autres éléments, la stratégie pédagogique et le contexte de séquence, sont définis par l'enseignant. La stratégie contient un ensemble de règles sous la forme 'SI...ALORS...SINON...'. Le 'SI' contient des contraintes sur les indicateurs du profil d'apprenant ainsi que sur les valeurs qui peuvent être trouvées dans le contexte 'live' (voir partie suivante pour des exemples de contraintes). Les parties 'ALORS' et 'SINON' caractérisent des activités que l'apprenant devra réaliser selon que la condition est vérifiée ou non (le 'SINON' est optionnel). Les activités disponibles qui peuvent être proposées aux apprenants sur la plateforme sont donc modélisées(à travers un modèle OKEP, sur lequel nous reviendrons, qui permet de contraindre les activités en fonction de différents paramètres).<br/>Le contexte de séquence permet à l'enseignant de donner des contraintes globales sur ce qui sera en sortie proposé à l'apprenant : nombres minimum et maximum d'activités à réaliser, temps minimum et maximum estimés que les activités doivent représenter... </p>
            <p>A chaque nouvelle séquence du MOOC (dans la plupart des MOOCs, 1 séquence = 1 semaine), l'enseignant définira une nouvelle stratégie pédagogique, et un nouveau contexte de séquence.</p>
            
            <p>Pour chaque apprenant, caractérisé par son profil et un contexte 'live', un premier processus est réalisé, qui permet de déterminer, dans la stratégie pédagogique, quelles sont les règles qui s'appliquent bien à lui. La sortie de ce processus est donc une liste de règles d'affectation, dont les parties 'ALORS' et 'SINON' contiennent des contraintes sur les activités de la plateforme.<p>
            <p>Enfin, à partir de ces règles, des listes d'activités sont générées pour chaque apprenant. Nous appelons ici cette liste d'activité une 'boussole', en référence à la manière dont les activités sont proposées dans le MOOC FOVEA, sur la plateforme  <a href="http://claco.univ-lyon1.fr">Claroline Connect</a></p>.
            
            <h3>Plus de détails sur...</h3>
            <p>Dans cette section, vous pourrez en apprendre plus sur chacun des éléments qui composent le modèle PERSUA2<sub>MOOC</sub> et son processus d'exploitation.</p>
            <h4>...les modèles utilisés</h4>
            <p>Dans PERSUA2<sub>MOOC</sub>, des profils d'apprenant, contextes d'utilisation,...sont utilisés. A chacun de ces éléments correspond un modèle, que nous avons formalisé en XMLSchema. Dans la mesure où chaque plateforme de MOOC est unique (les mêmes activités n'y sont pas disponibles, les informations collectées sur les apprenants et leurs actions peuvent différer), les profils d'apprenant ne vont pas être les mêmes sur chacune d'entre elles. De la même manière, pour deux MOOCs distincts hébergés sur la même plateforme, les informations exploitées sur les apprenants ne seront pas les mêmes, et dépendront avant tout des besoins exprimés par l'enseignant en vue de pouvoir opérer la personnalisation de la manière qui lui semble la plus efficace et pertinente.<br/>
            En conséquence, il existe, pour chacun des modèles réalisés, trois niveaux (voici une explication détaillée pour le profil d'apprenant, il en va de même pour les autres) :</p>
            <ul>
                <li>Un modèle pour les MOOCs en général, qui donne l'organisation globale du profil (voir la partie suivante) et des exemples d'indicateurs, dont certains seront certainement systématiquement repris aux niveaux suivants. Il est bien évidemment fortement adaptable, afin de convenir à toutes les plateformes et MOOCs qui souhaiteraient l'exploiter.</li>
                <li>Lorsqu'une plateforme de MOOC voudra mettre en place le processus de personnalisation, elle pourra ensuite modifier ce modèle d'apprenant qui lui est donné, pour l'adapter à sa situation propre. Dans la mesure où le modèle général, présenté dans le point précédent, a été réalisé en s'inspirant et en tenant fortement compte des plateformes actuelles de MOOC et des informations qu'elles collectent sur les apprenants, ce modèle adapté à une plateforme en particulier lui sera certainement très semblable.</li>
                <li>Enfin, le modèle devra une nouvelle fois être adapté pour répondre aux besoins et à la configuration d'un MOOC en particulier. Cette phase a énormément d'importance, puisque c'est à ce stade qu'il va falloir tenir compte des ressources apportées par les enseignants et disponibles pour les apprenants. De nombreux indicateurs vont ainsi faire leur apparition au sein du profil comme par exemple 'Nombre de consultations de la vidéo de présentation générale', 'Résultat au quiz n°1',... Les enseignants vont de plus avoir la possibilité d'exprimer leurs besoins précis sur le MOOC, et dire quelles informations pertinentes ils veulent voir émerger des traces.</li>
            </ul>
            <h4>...le profil d'apprenant</h4>
            <p>Le profil d'apprenant comporte cinq sections, qui vont de la plus générale à propos de l'apprenant, à la plus précise concernant ses interactions avec les ressources qui lui sont proposées au sein du MOOC. Voici, sur un schéma les cunq sections et l'ordre dans lequel elles apparaissent (nous revenons ensuite sur chacune d'entre elles avec plus de précisions).</p>
            <img src='img/learnerMoocProfile.PNG' alt='Structure profil apprenant dans les MOOCs'/>
            <h5>Section 'learnerInformation'</h5>
            <p>Cette première section contient des informations générales sur l'apprenant, qui ne sont pas extraites des traces, mais issues de questions qui peuvent être posées directement à l'apprenant (au moment de son inscription sur la plateforme ou lors de sa première connexion au MOOC). On y retrouve par exemple la date de naissance de l'apprenant, son sexe, sa situation professionnelle, son pays...(vous pourrez retrouver la liste complète des indicateurs qui sont pour l'instant présents au sein de cette section dans l'application elle-même).<p>
            <h5>La section 'knowledge'</h5>
            <p>Comme son nom l'indique, cette section va contenir des informations sur les connaissances et compétences de l'apprenant. Elle est subdivisée en deux sous-sections, la première concernant ses connaissances sur le sujet du MOOC (les indicateurs seront remplis en grande partie grâce à ses résultats aux quiz tout au long du MOOC). La deuxième a pour objet les outils qui sont utilisés dans le cadre du MOOC, comme par exemple, dans le cas de la programmation, la maîtrise d'un environnement de développement, ou dans d'autres matières la maîtrise d'outils comme une calculatrice. Dans la version générale du modèle de profil d'apprenant, tout ces indicateurs admettent des valeurs comprises entre 0 et 100 (100 signifiant que l'apprenant maîtrise totalement la connaissance ou compétence concernée).</p>
            <h5>La section 'behaviour'</h5>
            Dans cette section, ce sont les traces qui vont être exploitées de manière intensive pour obtenir des informations poussées sur l'apprenant,  des jugements qualitatifs concernant son comportement global sur la plateforme. Dans la version générale du modèle de profil, elle contient quelques indicateurs qui permettent de comprendre l'esprit et le type d'informations qui vont y être retrouvées. Un exemple d'indicateur que l'on y retrouve est 'studentPattern', issu de l'article  <a href="http://mfeldstein.com/combining-mooc-student-patterns-graphic-stanford-analysis/">Combining MOOC Student Patterns Graphic with Stanford Analysis</a>, par Phil Hill. Cet article indique, à partir de données tirées de MOOCs, que l'on peut segmenter les apprenants selon plusieurs catégories : </p>
            <ul>
                <li>les 'no-shows', qui ne se connectent jamais au MOOC après s'y être inscrits</li>,
                <li>les 'active completing', qui réussissent le MOOC tout en y participant activement, sur le forum par exemple,</li>
                <li>les 'passive completing' qui arrivent également à terminer le MOOC, mais sans trop participer au forum ni aux projets avancés,</li>
                <li>les 'auditing', qui suivent le cours durant sa majeure partie, mais ne répondent pas ou peu aux questionnaires et examens qui leur sont proposés,</li>
                <li>les 'disengaging' qui sont dans les catégories 'completing' au début du MOOC, mais vont progressivement moins fréquenter le MOOC,</li>
                <li>les 'sampling' qui vont simplement consulter quelques ressources du MOOC, mais sans aller vraiment plus loin</li>
            <p>Pour déterminer à quelle catégorie appartient un apprenant, les traces sont exploitées afin de savoir quelle quantité de ressources il consulte à chaque séquence, s'il se rend sur le forum ou non,...<br/>
            Pour les autres indicateurs, vous pouvez vous reporter au profil utilisé dans l'application, et cliquer sur les bulles d'information pour lire la documentation concernant chacun des indicateurs.
            
            Cette section pourra être très différente d'une plateforme de MOOC à l'autre, puisqu'elle dépend beacuoup des traces qui sont collectées, et surtout des traitements qui sont réalisés sur elles.
            </p>
            <h4>...le contexte 'live'</h4>
            <h4>...le contexte de séquence</h4>
            <h4>...la stratégie pédagogique</h4>
            <h4>...la caractérisation des activités</h4>
            
            <h2 id="appli">Présentation de l'application</h2>
            Etat actuel
            
            <h3>Perspectives d'évolution</h3>
            
            But plugin
            Permettre définition et calcul des indicateurs par l'enseignant
            Exploiter des générateurs d'exercice
            Donner des outils plus avancés à l'enseignant afin qu'il puisse définir sa stratégie pédagogique
            Déterminer des indicateurs plus complexes, comme par exemple ceux qui concernent le travail collaboratif.
            Détecter de manière dynamique les difficultés rencontrées par les utilisateurs.
            <p id="footnotePersua2"><a href="http://hal.archives-ouvertes.fr/docs/00/69/19/84/PDF/Lefevre-Marie-EIAH2011.pdf">Article PERSUA2</a></p>
        </div>
        <script type="text/javascript" src="js/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
	</body>
	
	
    
</html>