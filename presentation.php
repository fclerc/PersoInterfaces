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
            <p>Les deux autres éléments, la stratégie pédagogique et le contexte de séquence, sont définis par l'enseignant. La stratégie contient un ensemble de règles sous la forme 'SI...ALORS...SINON...'. Le 'SI' contient des contraintes sur les indicateurs du profil d'apprenant ainsi que sur les valeurs qui peuvent être trouvées dans le contexte 'live' (voir partie suivante pour des exemples de contraintes et de règles complètes). Les parties 'ALORS' et 'SINON' caractérisent des activités que l'apprenant devra réaliser selon que la condition est vérifiée ou non (le 'SINON' est optionnel). Les activités disponibles qui peuvent être proposées aux apprenants sur la plateforme sont donc modélisées(à travers un modèle OKEP, sur lequel nous reviendrons, qui permet de contraindre les activités en fonction de différents paramètres).<br/>Le contexte de séquence permet à l'enseignant de donner des contraintes globales sur ce qui sera en sortie proposé à l'apprenant : nombres minimum et maximum d'activités à réaliser, temps minimum et maximum estimés que les activités doivent représenter... </p>
            <p>A chaque nouvelle séquence du MOOC (dans la plupart des MOOCs, 1 séquence = 1 semaine), l'enseignant définira une nouvelle stratégie pédagogique, et un nouveau contexte de séquence.</p>
            
            <p>Pour chaque apprenant, caractérisé par son profil et un contexte 'live', un premier processus est réalisé, qui permet de déterminer, dans la stratégie pédagogique, quelles sont les règles qui s'appliquent bien à lui. La sortie de ce processus est donc une liste de règles d'affectation, dont les parties 'ALORS' et 'SINON' contiennent des contraintes sur les activités de la plateforme.<p>
            <p>Enfin, à partir de ces règles, des listes d'activités sont générées pour chaque apprenant. Nous appelons ici cette liste d'activité une 'boussole', en référence à la manière dont les activités sont proposées dans le MOOC FOVEA, sur la plateforme  <a href="http://claco.univ-lyon1.fr">Claroline Connect.</a></p>
            <p>Pour plus de détails sur la modélisation complète, vous pouvez consulter la partie ci-dessous. Sinon, vous pouvez vous rendre directement à la section <a href="#appli">Présentation de l'application</a>.</p>
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
            <h5>La section 'learnerInformation'</h5>
            <p>Cette première section contient des informations générales sur l'apprenant, qui ne sont pas extraites des traces, mais issues de questions qui peuvent être posées directement à l'apprenant (au moment de son inscription sur la plateforme ou lors de sa première connexion au MOOC). On y retrouve par exemple la date de naissance de l'apprenant, son sexe, sa situation professionnelle, son pays...(vous pourrez retrouver la liste complète des indicateurs qui sont pour l'instant présents au sein de cette section dans l'application elle-même).<p>
            
            <h5>La section 'knowledge'</h5>
            <p>Comme son nom l'indique, cette section va contenir des informations sur les connaissances et compétences de l'apprenant. Elle est subdivisée en deux sous-sections, la première concernant ses connaissances sur le sujet du MOOC (les indicateurs seront remplis en grande partie grâce à ses résultats aux quiz tout au long du MOOC). La deuxième a pour objet les outils qui sont utilisés dans le cadre du MOOC, comme par exemple, dans le cas de la programmation, la maîtrise d'un environnement de développement, ou dans d'autres matières la maîtrise d'outils comme une calculatrice. Dans la version générale du modèle de profil d'apprenant, tout ces indicateurs admettent des valeurs comprises entre 0 et 100 (100 signifiant que l'apprenant maîtrise totalement la connaissance ou compétence concernée).</p>
            
            <h5>La section 'behaviour'</h5>
            Dans cette section, ce sont les traces qui vont être exploitées de manière intensive pour obtenir des informations poussées sur l'apprenant,  des jugements qualitatifs sur la manière dont il apprend. Dans la version générale du modèle de profil, elle contient quelques indicateurs qui permettent de comprendre l'esprit et le type d'informations qui vont y être retrouvées. Un exemple d'indicateur que l'on y retrouve est 'studentPattern', issu de l'article  <a href="http://mfeldstein.com/combining-mooc-student-patterns-graphic-stanford-analysis/">Combining MOOC Student Patterns Graphic with Stanford Analysis</a>, par Phil Hill. Cet article indique, à partir de données tirées de MOOCs, que l'on peut segmenter les apprenants selon plusieurs catégories : </p>
            <ul>
                <li>les 'no-shows', qui ne se connectent jamais au MOOC après s'y être inscrits</li>,
                <li>les 'active completing', qui réussissent le MOOC tout en y participant activement, sur le forum par exemple,</li>
                <li>les 'passive completing' qui arrivent également à terminer le MOOC, mais sans trop participer au forum ni aux projets avancés,</li>
                <li>les 'auditing', qui suivent le cours durant sa majeure partie, mais ne répondent pas ou peu aux questionnaires et examens qui leur sont proposés,</li>
                <li>les 'disengaging' qui sont dans les catégories 'completing' au début du MOOC, mais vont progressivement moins fréquenter le MOOC,</li>
                <li>les 'sampling' qui vont simplement consulter quelques ressources du MOOC, mais sans aller vraiment plus loin</li>
            </ul>
            <p>Pour déterminer à quelle catégorie appartient un apprenant, les traces sont exploitées afin de savoir quelle quantité de ressources il consulte à chaque séquence, s'il se rend sur le forum ou non,...<br/>
            Pour les autres indicateurs, vous pouvez vous reporter au profil utilisé dans l'application, et cliquer sur les bulles d'information pour lire la documentation concernant chacun des indicateurs.
            
            Cette section pourra être très différente d'une plateforme de MOOC à l'autre, puisqu'elle dépend beacuoup des traces qui sont collectées, et surtout des traitements qui sont réalisés sur elles.</p>
            
            <h5>La section 'moocInteractions'</h5>
            <p>Cette section concerne les indicateurs se rapportant aux interactions de l'apprenant avec la plateforme de MOOC, comme la dynamique de son activité. Cette section peut se rapprocher de la précédente, mais se différencie par un aspect essentiel : les indicateurs qu'elle contient sont avant tout quantitatifs (alors que, comme nous l'avons vu, la section 'behaviour' contient des indicateurs permettant de réaliser des jugements qualitatifs sur l'apprenant).<br/>
            Au sein de cette section on trouvera par exemple des indicateurs permettant de savoir, pour chaque jour de la semaine, combien de temps l'apprenant a passé sur la plateforme (cela permettra par exemple de savoir s'il apprend plutôt le week-end, le mardi...). Les mêmes indicateurs sont présents concernant son activité durant une même journée (est-il connecté de 14h à 18h, de 18h à 22h?,...).</p>
            
            <h5>La section 'resourcesInteractions'</h5>
            <p>Cette dernière section concerne les interactions de l'apprenant avec les ressources directement : pour chacune des ressources sur lesquelles l'enseignant désire avoir des informations, des indicateurs contiendront le nombre de fois où un apprenant l'a consultée, le temps qu'il a passé à consulter cette ressource (si des algorihtmes sont capables sur la plateforme de le calculer), et le taux de complétion (pour une vidéo par exemple, savoir si l'apprenant l'a entièrement visionnée, ou s'est arrêté à x%). On pourra trouver d'autres indicateurs sur les interactions de l'apprenant avec les ressources, comme par exemple le nombre de fois qu'un apprenant clique sur le bouton 'pause' lorsqu'il visualise une vidéo.</p>
            <p>Enfin, certains indicateurs permettront d'étudier les interactions de l'apprenant dans un contexte donné. L'exemple qui est donné dans notre modèle général concerne les devoirs de l'apprenant : des indicateurs permettront de savoir combien il a passé de temps sur le forum, ou sur les ressources du cours, lorsqu'il était en train de répondre à des questions qui lui sont posées dans le MOOC.</p>
            
            <p>Une rapide note concernant ce que nous appelons 'ressource' : dans le cadre de notre modélisation, tout ce qui est mis à disposition de l'apprenant est appelé 'ressource', que ce soit une vidéo, le forum,etc. Une séquence du MOOC (qui peut par exemple contenir 3 vidéos, 1 quiz, un texte,...) est elle-même appelée 'ressource'. Cela permet ainsi d'utiliser le même type d'indicateur pour savoir combien de temps un apprenant a passé sur une vidéo, et combien de temps il a passé sur une séquence en général.</p>
            
            <h4>...le contexte 'live'</h4>
            <p>Ce contexte 'live' apporte des informations supplémentaires sur l'apprenant et sur le MOOC au moment où il se connecte à la plateforme, et qui ne sont pas contenues dans son profil.
            Par rapport au profil d'apprenant, ce modèle est relativement léger, et les informations qu'il contient sont globalement toutes celles que peut obtenir le serveur sur l'apprenant et le MOOC. Il est divisé en deux parties :</p>
            <ul>
                <li>La partie 'environmentContext' qui contient les informations générales sur l'environnement du MOOC. Dans le modèle général, seules deux informations sont contenues : la date et l'heure, ainsi que des chiffres sur le type et le nombre de personnes qui sont connectées à un instant donné : le nombre d'apprenants, le nombre d'enseignants, d'administrateurs,... Cette section peut être enrichie en fonction des plateformes et de leurs fonctionnalités. Par exemple, si une plateforme comporte un outil de chat, un indicateur pourra contenir le nombre de connectés.</li>
                <li>La partie 'learnerLiveContext' contient les informations disponibles sur l'apprenant lorsqu'il se connecte, on y retrouve le type d'appareil qu'il utilise (ordinateur, tablette, smartpgone), son système d'exploitation, le navigateur, son adresse IP... D'autres indicateurs plus avancés peuvent être ajoutés s'ils sont disponibles, comme la bande passante dont il dispose (cela peut avoir son importance si des vidéos sont à visionner), le temps disponible pour l'apprenant (on pourrait lui demander au moment où il se connecte le temps qu'il a devant lui pour cette session, afin de générer des activités qui répondront à cette contrainte),...</li>
            </ul>
            
            <h4>...le contexte de séquence</h4>
            <p>Ce contexte est d'une toute autre nature que celui vu précédemment, puisqu'il ne va pas être calculé de manière automatique, mais défini par l'enseignant du MOOC à chaque séquence. Il s'agit de contraintes globales sur les activités qui vont être générées pour chaque apprenant. Ces contraintes vont concerner le nombre d'activités réalisées par un apprenant, ou le temps (théorique) qu'il devra passer sur le MOOC durant la séquence. Pour chacune de ces deux grandeurs, un minimum et un maximum seront donnés.<br/>
            Le contexte de séquence contient un autre élément important, à savoir le 'contexte' dont les activités doivent être tirées. Le plus souvent, cela permettra à l'enseignant d'exprimer une contrainte comme 'Je veux que toutes les activités soient issues de la séquence 2', ou encore 'Je veux que toutes les activités soient issues de la catégorie "débutant" ' (lorsqu'il définit ses ressources, l'enseignant a la possibilité de leur attacher des catégories, qu'il nomme comme il le souhaite).</p>
            
            <h4>...la caractérisation des activités</h4>
            <p>Avant d'étudier plus en détail la notion de stratégie pédagogique, il nous faut revenir sur un point essentiel de notre modélisation, à savoir la caractérisation des fonctionnalités disponibles sur une plateforme de MOOC, et la manière dont on peut les paramétrer. Pour cela, on utilise un modèle appelé modèle 'OKEP' de la plateforme, élaboré à partir du méta-modèle 'AKEPI' (pour une présentation complète de ces concepts dans le cadre des EIAH, vous pouvez vous référer à la <a href="http://liris.cnrs.fr/Documents/Liris-4522.pdf">thèse de Marie Lefevre</a>. Nous n'avons ici exploité qu'une partie des possibilités de ce modèle, qui suffisent à notre modélisation : la caractérisation des activités disponibles (dans un premier temps nous ne souhaitons pas paramétrer directement les plateformes de MOOCs, mais simplement offrir aux apprenannts une boussole, une liste d'activités).</p>
            <p>Après une étude complète de plusieurs plateformes de MOOCs, nous avons déduit les 4 activités qui sont présentes sur chacune d'entre elles, et les paramètres que nous pouvons utiliser en relation avec elles : (nous n'indiquons les paramètres que pour la première d'entre elles, pour les autres toutes les informations peuvent être trouvées au sein de l'application)</p>
            <ul>
                <li><strong>Apprentissage</strong> : cette activité concerne la consultation d'une ressource par un apprenant. Afin de choisir quelle ressource doit être consultée, plusieurs paramètres peuvent être utilisés par l'enseignant (tous sont optionnels, l'enseignant peut donc utiliser les paramètres qu'il souhaite) :
                    <ul>
                        <li><strong>Nom</strong> : ce paramètre est le plus simple et le plus direct, on désigne la ressource directement par son nom (éventuellement son URI).</li>
                        <li><strong>Statut</strong> : une ressource peut avoir trois statuts différents : Obligatoire, Facultatif (ceux qui ont des connaissances déjà avancées du sujet n'auront pas besoin de la consulter), Bonus (pour les apprenants en avance sur le MOOC, on leur propose des activités plus compliquées, amusantes,...plutôt que de les laisser avec une boussole vide).</li>
                        <li><strong>Séquence</strong> : permet de désigner directement la séquence dans laquelle aller chercher la ressources.</li>
                        <li><strong>Catégorie</strong> : à chaque ressources peuvent être attachées des catégories (des 'tags'), comme 'débutant', 'c++',etc. (tout ce que l'enseignant souhaite ajouter).</li>
                        <li><strong>Durée</strong> : le temps estimé, en minutes, que doit durer l'activité</li>
                        <li><strong>Difficulté</strong> : ce paramètre peut aller de 0 (très facile) à 5 (très difficile)</li>
                        <li><strong>Type</strong>  : tout simplement le type de la ressource (vidéo, image, texte...)</li>
                    </ul>
                    <p>S'il utilise un de ces paramètres, l'enseignant devra bien sûr avoir au préalable renseigné leurs valeurs pour chacune des ressources utilisées dans le cours (ou du moins pour celles qu'il souhaite pouvoir désigner avec ces paramètres).</p>
                </li>
                <li><strong>Social</strong> : l'apprenant est invité à se rendre sur les réseaux sociaux, ou sur le forum du MOOC.</li>
                <li><strong>Exercice</strong> : cette activité est la réalisation d'un exercice par l'apprenant</li>
                <li><strong>Message</strong> : il ne s'agit pas d'une activité à proprement parlé, mais se manifestera tout de même dans la boussole : il s'agit du simple affichage d'un message à destination de l'apprenant (pour lui dire bonjour, le féliciter, l'encourager,...).</li>
            </ul>
            
            <h4>...la stratégie pédagogique</h4>
            <p>C'est avec la stratégie pédagogique que l'enseignant va pouvoir exprimer, sous forme de règles, la manière dont il souhaite personnaliser son MOOC à chacun des apprenants, en fonction des valeurs prises par les indicateurs dans son profil. Afin de bien comprendre la manière dont sont définies les règles, prenons l'exemple d'un cours de programmation en Python, qui contient deux ressources : une vidéo 'boucle for' et un quiz 'Quiz1' qui permet de tester les connaissances sur la boucle for. Supposons que l'enseignant, afin de tester le niveau des apprenants, leur mettre directement, dès la première séquence, le Quiz1 (sans leur montrer la vidéo). Le résultat à ce quiz remplit directement un indicateur dans la partie 'knowledge' du profil d'apprenant, l'indicateur 'RésultatBoucleFor'. Voici la règle que peut alors définir l'enseignant :</p>
            <p><strong>SI RésultatBoucleFor &lt; 60 ALORS regarder vidéo 'bouclefor'</strong>.</p>
            <p>La partie SINON étant optionnelle, nous ne l'avons pas fait figurer ici, mais on pourrait avoir :</p>
            <p><strong>SINON aller sur le FORUM avec Action = Answer</strong>, on invite ainsi l'apprenant à aller sur le forum, et répondre aux questions que se posent ceux qui n'ont éventuellement pas compris certaines parties du cours (bien évidemment, dans la boussole le tout sera sous forme textuelle et bien plus explicite). On pourrait aussi lui proposer de faire un exercice en relation avec la boucle for qui soit d'un niveau plus avancé, plus ludique (calcul des termes de la suite de Fibonacci,...)</p>
            <p>Voici donc comment se décomposent les règles de la stratégie pédagogique :</p>
            <p><strong>SI {Contrainte sur profil} ALORS {Activité avec paramètres} [SINON {Activité avec paramètres}]</strong>.</p>
            <p>A partir de cette construction simple, l'enseignant a un champ de possibilités très large pour permettre aux apprenants d'avoir une boussole personnalisée, avec les activités qui leur convient au mieux.</p>
            
            <h4>Pour les plus courageux...</h4>
            <p>Pour ceux qui souhaiteraient avoir une vision complète de tous les modèles évoqués ici, voici tout simplement un  lien vers les différents fichiers XMLSchema qui nous ont permis de formaliser notre modèle :</p>
            <ul>
                <li><a href="resources/learnerMoocProfile.xsd">Modèle de profil d'apprenant</a></li>
                <li><a href="resources/context.xsd">Modèle de contexte</a> (qui regroupe contexte de séquence et contexte 'live')</li>
                <li><a href="resources/foveaPedagogicalProperties.xml">Modèle OKEP de base</a> (issu du modèle OKEP, et relativement simple à comprendre, reegroupant les activités et leurs paramètres)</li>
                
            </ul>
            
            Voici également un exemple de <a href="resources/Sequence1.xml">stratégie pédagogique</a>, stockée au format XML (afffichez la source de la page si l'affichage n'est pas satisfaisant).
            
            <h3>Bilan</h3>
            <p>Tout ceci demande donc un gros effort de réflexion de la part des enseignants et de la part de toute l'équipe de conception d'un MOOC, afin d'identifier quelles informations judicieuses permettront une personnalisation efficace. Bien entendu, tout cela doit se faire en collaboration avec les concepteurs de la plateforme, qui seront les plus à mêmes de savoir quelles informations peuvent ou non être extraites des traces générées par les apprenants durant leurs activités.</p>
            
            
            
            
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