<!DOCTYPE HTML>
<html>
	<head>
        <title>Personnalisation et MOOCs</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<link href="css/bootstrap.css" type="text/css" rel="stylesheet"/>
		<link href="css/main.css" type="text/css" rel="stylesheet"/>
		
		
	</head>
    
    <body>
        <div class="container">
            <h1>Mise en place de la personnalisation dans le cadre des MOOCs</h1>
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
            <p>Dans PERSUA2<sub>MOOC</sub>, des profils d'apprenant, contextes d'utilisation,...sont utilisés. A chacun de ces éléments correspond un modèle, que nous avons formalisé en XMLSchema. 3 niveaux de modèles,...</p>
            <h4>...le profil d'apprenant</h4>
            
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