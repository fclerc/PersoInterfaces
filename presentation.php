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
            <p>Le principe est simple : au sein d'un MOOC, les apprenants vont réaliser des activités, au cours desquelles toutes leurs actions vont pouvoir être tracées. Grâce aux traces générées par ces interactions avec la plateforme, un profil d'apprenant va pouvoir être généré pour chacun. L'équipe pédagogique en charge du MOOC va de son côté définir une stratégie pédagogique. Cette stratégie pédagogique va permettre de déterminer de manière automatique pour chacun des apprenants de nouvelles activités et de nouveaux parcours, en fonction des informations contenues dans son profil. Puis, le cycle va pouvoir recommencer, puisque de nouvelles traces vont être générées par les apprenants lorsqu'ils réaliseront ces activités.</p>
            <p>Dans ce projet, nous nous intéressons particulièrement au processus de définition de sa stratégie pédagogique par l'équipe pédagogique, afin de lui permettre d'exprimer au mieux les objectifs pédagogiques qu'elle a au cours du MOOC.</p>
            
            
            
            <p>A partir du modèle PERSUA2<sup><a href="#footnotePersua2">1</a></sup>, une adaptation pour les MOOCs a été réalisée afin d'exploiter du mieux possible les perspectives offertes par les plateformes. Voici le processus d'exploitation qui est associé, et qui permet de comprendre la façon dont fonctionnera, à terme, la présente application : </p>
            <img src="img/processusPersua2mooc.PNG" alt="Processus d'exploitation de PERSUA2mooc"/>
            <p>Voici une explication sommaire, pour plus de détails sur chacun des éléments, se reporter à la section suivante : </p>
            <p>En entrée du processus se trouvent quatre éléments. Deux d'entre eux vont permettre de caractériser l'apprenant, et sont calculés de manière automatique : le profil, et le contexte 'live'. Le profil contient des indicateurs qui permettent de décrire qui est l'apprenant ainsi que la manière dont il a interagi avec le MOOC depuis le début de celui-ci. Le contexte 'live' contient quant à lui des informations qui vont permettre de caractériser la situation à un instant donnée, lorsque l'apprenant se connecte à la plateforme : l'heure précise, l'appareil avec lequel l'apprenant se connecte, la bande passante dont il dispose,...ainsi que des informations sur l'environnement du MOOC en général, comme par exemple le nombre d'apprenants connectés.</p>
            <p>Les deux autres éléments, la stratégie pédagogique et le contexte de séquence, sont définis par l'équipe pédagogique. La stratégie contient un ensemble de règles sous la forme 'SI...ALORS...SINON...'. Le 'SI' contient des contraintes sur les indicateurs du profil d'apprenant ainsi que sur les valeurs qui peuvent être trouvées dans le contexte 'live' (voir partie suivante pour des exemples de contraintes et de règles complètes). Les parties 'ALORS' et 'SINON' caractérisent des activités que l'apprenant devra réaliser selon que la condition est vérifiée ou non (le 'SINON' est optionnel). Les activités disponibles qui peuvent être proposées aux apprenants sur la plateforme sont  modélisées (à travers un modèle OKEP, sur lequel nous reviendrons, qui permet de contraindre les activités en fonction de différents paramètres).<br/>Le contexte de séquence permet à l'équipe pédagogique de donner des contraintes globales sur ce qui sera en sortie proposé à l'apprenant : nombres minimum et maximum d'activités à réaliser, temps minimum et maximum estimés que les activités doivent représenter... </p>
            <p>A chaque nouvelle séquence du MOOC (dans la plupart des MOOCs, 1 séquence = 1 semaine), l'équipe définira si elle le souhaite une nouvelle stratégie pédagogique, et un nouveau contexte de séquence. Cependant, une grande liberté est laissée à ce niveau : il est  par exemple tout à fait possible de conserver la même stratégie pédagogique tout au long du MOOC, et ne faire varier que le contexte de séquence.</p>
            
            <p>Pour chaque apprenant, caractérisé par son profil et un contexte 'live', un premier processus est réalisé, qui permet de déterminer, dans la stratégie pédagogique, quelles sont les règles qui s'appliquent bien à lui. La sortie de ce processus est donc une liste de règles d'affectation, dont les parties 'ALORS' et 'SINON' contiennent des contraintes sur les activités de la plateforme.<p>
            <p>Enfin, à partir de ces règles, des listes d'activités sont générées pour chaque apprenant. Nous appelons ici cette liste d'activité une 'boussole', en référence à la manière dont les activités sont proposées dans le MOOC FOVEA, sur la plateforme  <a href="http://claco.univ-lyon1.fr">Claroline Connect.</a>. Comme nous l'avons déjà évoqué précédemment, il s'agit uniquement de recommandations qui sont données à l'apprenant (aucun paramétrage de la plateforme n'est réalisé, et l'apprenant reste in fine libre de choisir les activités qu'il souhaire réaliser). Cependant, notre modèle reste évolutif, et si un jour une plateforme de MOOC le permet, il sera tout à fait possible de transformer ces recommandations en contraintes : il 'suffira' pour la personnalisation de savoir comment paramétrer concrètement et automatiquement la plateforme qui offre de telles possibilités</p>
            
            <p>Pour plus de détails sur la modélisation complète, vous pouvez consulter la partie ci-dessous. Sinon, vous pouvez vous rendre directement à la section <a href="#appli">Présentation de l'application</a>.</p>
            <h3>Plus de détails sur...</h3>
            <p>Dans cette section, vous pourrez en apprendre plus sur chacun des éléments qui composent le modèle PERSUA2<sub>MOOC</sub> et son processus d'exploitation.</p>
            
            <h4>...les modèles utilisés</h4>
            <p>Dans PERSUA2<sub>MOOC</sub>, des profils d'apprenant, contextes d'utilisation,...sont utilisés. A chacun de ces éléments correspond un modèle, que nous avons formalisé en XMLSchema. Dans la mesure où chaque plateforme de MOOC est unique (les mêmes activités n'y sont pas disponibles, les informations collectées sur les apprenants et leurs actions peuvent différer), les profils d'apprenant ne vont pas être les mêmes sur chacune d'entre elles. De la même manière, pour deux MOOCs distincts hébergés sur la même plateforme, les informations exploitées sur les apprenants ne seront pas les mêmes, et dépendront avant tout des besoins exprimés par l'équipe pédagogique en vue de pouvoir opérer la personnalisation de la manière qui lui semble la plus efficace et pertinente.<br/>
            En conséquence, il existe, pour chacun des modèles réalisés, trois niveaux (voici une explication détaillée pour le profil d'apprenant, il en va de même pour les autres) :</p>
            <ul>
                <li>Un modèle pour les MOOCs en général, qui donne l'organisation globale du profil (voir la partie suivante) et des exemples d'indicateurs, dont certains seront certainement systématiquement repris aux niveaux suivants. Il est bien évidemment fortement adaptable, afin de convenir à toutes les plateformes et MOOCs qui souhaiteraient l'exploiter.</li>
                <li>Lorsque les concepteurs ou administrateurs d'une plateforme de MOOC voudront mettre en place le processus de personnalisation, ils pourront ensuite modifier ce modèle d'apprenant qui est donné, pour l'adapter à leur situation propre. Dans la mesure où le modèle général, présenté dans le point précédent, a été réalisé en s'inspirant et en tenant fortement compte des plateformes actuelles de MOOC et des informations qu'elles collectent sur les apprenants, ce modèle adapté à une plateforme en particulier lui sera certainement très semblable.</li>
                <li>Enfin, le modèle devra une nouvelle fois être adapté pour répondre aux besoins et à la configuration d'un MOOC en particulier. Cette phase a énormément d'importance, puisque c'est à ce stade qu'il va falloir tenir compte des ressources apportées par l'équipe pédagogique et disponibles pour les apprenants. De nombreux indicateurs vont ainsi faire leur apparition au sein du profil comme par exemple 'Nombre de consultations de la vidéo de présentation générale', 'Résultat au quiz n°1',... L'équipe pédagogique va de plus avoir la possibilité d'exprimer ses besoins précis sur le MOOC, et dire quelles informations pertinentes elle veut voir émerger des traces.</li>
            </ul>
            
            <h4>...le profil d'apprenant</h4>
            <p>Le profil d'apprenant comporte cinq sections, qui vont de la plus générale à propos de l'apprenant, à la plus précise concernant ses interactions avec les ressources qui lui sont proposées au sein du MOOC. Voici, sur un schéma les cinq sections et l'ordre dans lequel elles apparaissent (nous revenons ensuite sur chacune d'entre elles avec plus de précisions).</p>
            <img src='img/learnerMoocProfile.PNG' alt='Structure profil apprenant dans les MOOCs'/>
            <h5>La section 'learnerInformation'</h5>
            <p>Cette première section contient des informations générales sur l'apprenant, qui ne sont pas extraites des traces, mais issues de questions qui peuvent être posées directement à l'apprenant (au moment de son inscription sur la plateforme ou lors de sa première connexion au MOOC). On y retrouve par exemple la date de naissance de l'apprenant, son sexe, sa situation professionnelle, son pays... (vous pourrez retrouver la liste complète des indicateurs qui sont pour l'instant présents au sein de cette section dans l'application elle-même).<p>
            
            <h5>La section 'knowledge'</h5>
            <p>Comme son nom l'indique, cette section va contenir des informations sur les connaissances et compétences de l'apprenant. Elle est subdivisée en deux sous-sections, la première concernant ses connaissances sur le sujet du MOOC (les indicateurs seront remplis en grande partie grâce à ses résultats aux quiz tout au long du MOOC, mais aussi grâce à d'autres informations comme les données issues de la correction par les pairs). La deuxième a pour objet les outils qui sont utilisés dans le cadre du MOOC, comme par exemple, dans le cas de la programmation, la maîtrise d'un environnement de développement, ou dans d'autres matières la maîtrise d'outils comme une calculatrice. Dans la version générale du modèle de profil d'apprenant, tous ces indicateurs admettent des valeurs comprises entre 0 et 100 (100 signifiant que l'apprenant maîtrise totalement la connaissance ou compétence concernée).</p>
            
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
            
            Cette section pourra être très différente d'une plateforme de MOOC à l'autre, puisqu'elle dépend beaucoup des traces qui sont collectées, et surtout des traitements qui sont réalisés sur elles.</p>
            
            <h5>La section 'moocInteractions'</h5>
            <p>Cette section concerne les indicateurs se rapportant aux interactions de l'apprenant avec la plateforme de MOOC, comme la dynamique de son activité. Cette section peut se rapprocher de la précédente, mais se différencie par un aspect essentiel : les indicateurs qu'elle contient sont avant tout quantitatifs (alors que, comme nous l'avons vu, la section 'behaviour' contient des indicateurs permettant de réaliser des jugements qualitatifs sur l'apprenant).<br/>
            Au sein de cette section on trouvera par exemple des indicateurs permettant de savoir, pour chaque jour de la semaine, combien de temps l'apprenant a passé sur la plateforme (cela permettra par exemple de savoir s'il apprend plutôt le week-end, le mardi...). Les mêmes indicateurs sont présents concernant son activité durant une même journée (est-il connecté de 14h à 18h, de 18h à 22h?,...).</p>
            
            <h5>La section 'resourcesInteractions'</h5>
            <p>Cette dernière section concerne les interactions de l'apprenant avec les ressources directement : pour chacune des ressources sur lesquelles l'équipe pédagogique désire avoir des informations, des indicateurs contiendront le nombre de fois où un apprenant l'a consultée, le temps qu'il a passé à consulter cette ressource (si des algorithmes sont capables sur la plateforme de le calculer), et le taux de complétion (pour une vidéo par exemple, savoir si l'apprenant l'a entièrement visionnée, ou s'est arrêté à x%). On pourra trouver d'autres indicateurs sur les interactions de l'apprenant avec les ressources, comme par exemple le nombre de fois qu'un apprenant clique sur le bouton 'pause' lorsqu'il visualise une vidéo.</p>
            <p>Enfin, certains indicateurs permettront d'étudier les interactions de l'apprenant dans un contexte donné. L'exemple qui est donné dans notre modèle général concerne les devoirs de l'apprenant : des indicateurs permettront de savoir combien il a passé de temps sur le forum, ou sur les ressources du cours, lorsqu'il était en train de répondre à des questions qui lui sont posées dans le MOOC.</p>
            
            <p>Une rapide note concernant ce que nous appelons 'ressource' : dans le cadre de notre modélisation, tout ce qui est mis à disposition de l'apprenant est appelé 'ressource', que ce soit une vidéo, le forum, etc. Une séquence du MOOC (qui peut par exemple contenir 3 vidéos, 1 quiz, un texte,...) est elle-même appelée 'ressource'. Cela permet ainsi d'utiliser le même type d'indicateur pour savoir combien de temps un apprenant a passé sur une vidéo, et combien de temps il a passé sur une séquence en général.</p>
            
            <h4>...le contexte 'live'</h4>
            <p>Ce contexte 'live' apporte des informations supplémentaires sur l'apprenant et sur le MOOC au moment où il se connecte à la plateforme, et qui ne sont pas contenues dans son profil.
            Par rapport au profil d'apprenant, ce modèle est relativement léger, et les informations qu'il contient sont globalement toutes celles que peut obtenir le serveur sur l'apprenant et le MOOC. Il est divisé en deux parties :</p>
            <ul>
                <li>La partie 'environmentContext' qui contient les informations générales sur l'environnement du MOOC. Dans le modèle général, seules deux informations sont contenues : la date et l'heure, ainsi que des chiffres sur le type et le nombre de personnes qui sont connectées à un instant donné : le nombre d'apprenants, le nombre d'enseignants, d'administrateurs,... Cette section peut être enrichie en fonction des plateformes et de leurs fonctionnalités. Par exemple, si une plateforme comporte un outil de chat, un indicateur pourra contenir le nombre de connectés.</li>
                <li>La partie 'learnerLiveContext' contient les informations disponibles sur l'apprenant lorsqu'il se connecte, on y retrouve le type d'appareil qu'il utilise (ordinateur, tablette, smartpgone), son système d'exploitation, le navigateur, son adresse IP... D'autres indicateurs plus avancés peuvent être ajoutés s'ils sont disponibles, comme la bande passante dont il dispose (cela peut avoir son importance si des vidéos sont à visionner), le temps disponible pour l'apprenant (on pourrait lui demander au moment où il se connecte le temps qu'il a devant lui pour cette session, afin de générer des activités qui répondront à cette contrainte),...</li>
            </ul>
            
            <h4>...le contexte de séquence</h4>
            <p>Ce contexte est d'une toute autre nature que celui vu précédemment, puisqu'il ne va pas être calculé de manière automatique, mais défini par l'équipe pédagogique du MOOC à chaque séquence. Il s'agit de contraintes globales sur les activités qui vont être générées pour chaque apprenant. Ces contraintes vont concerner le nombre d'activités réalisées par un apprenant, ou le temps (théorique) qu'il devra passer sur le MOOC durant la séquence. Pour chacune de ces deux grandeurs, un minimum et un maximum seront donnés.<br/>
            Le contexte de séquence contient un autre élément important, à savoir le 'contexte' dont les activités doivent être tirées. Le plus souvent, cela permettra à l'équipe pédagogique d'exprimer une contrainte comme 'Je veux que toutes les activités soient issues de la séquence 2', ou encore 'Je veux que toutes les activités soient issues de la catégorie "débutant" ' (lorsqu'elle définit ses ressources, l'équipe pédagogique a la possibilité de leur attacher des catégories, qu'elle nomme comme elle le souhaite).</p>
            
            <h4>...la caractérisation des activités</h4>
            <p>Avant d'étudier plus en détail la notion de stratégie pédagogique, il nous faut revenir sur un point essentiel de notre modélisation, à savoir la caractérisation des fonctionnalités disponibles sur une plateforme de MOOC, et la manière dont on peut les paramétrer. Pour cela, on utilise un modèle appelé modèle 'OKEP' de la plateforme, élaboré à partir du méta-modèle 'AKEPI' (pour une présentation complète de ces concepts dans le cadre des EIAH, vous pouvez vous référer à la <a href="http://liris.cnrs.fr/Documents/Liris-4522.pdf">thèse de Marie Lefevre</a>. Nous n'avons ici exploité qu'une partie des possibilités de ce modèle, qui suffisent à notre modélisation : la caractérisation des activités disponibles (dans un premier temps nous ne souhaitons pas paramétrer directement les plateformes de MOOCs, mais simplement offrir aux apprenants une boussole, une liste d'activités).</p>
            <p>Après une étude complète de plusieurs plateformes de MOOCs, nous avons déduit les 4 activités qui sont présentes sur chacune d'entre elles, et les paramètres que nous pouvons utiliser en relation avec elles : (nous n'indiquons les paramètres que pour la première d'entre elles, pour les autres toutes les informations peuvent être trouvées au sein de l'application)</p>
            <ul>
                <li><strong>Apprentissage</strong> : cette activité concerne la consultation d'une ressource par un apprenant. Afin de choisir quelle ressource doit être consultée, plusieurs paramètres peuvent être utilisés par l'équipe pédagogique (tous sont optionnels, l'équipe pédagogique peut donc utiliser les paramètres qu'elle souhaite) :
                    <ul>
                        <li><strong>Nom</strong> : ce paramètre est le plus simple et le plus direct, on désigne la ressource directement par son nom (éventuellement son URI).</li>
                        <li><strong>Statut</strong> : une ressource peut avoir trois statuts différents : Obligatoire, Facultatif (ceux qui ont des connaissances déjà avancées du sujet n'auront pas besoin de la consulter), Bonus (pour les apprenants en avance sur le MOOC, on leur propose des activités plus compliquées, amusantes,...plutôt que de les laisser avec une boussole vide).</li>
                        <li><strong>Séquence</strong> : permet de désigner directement la séquence dans laquelle aller chercher la ressource.</li>
                        <li><strong>Catégorie</strong> : à chaque ressources peuvent être attachées des catégories (des 'tags'), comme 'débutant', 'c++', etc. (tout ce que l'équipe pédagogique souhaite ajouter).</li>
                        <li><strong>Durée</strong> : le temps estimé, en minutes, que doit durer l'activité</li>
                        <li><strong>Difficulté</strong> : ce paramètre peut aller de 0 (très facile) à 5 (très difficile)</li>
                        <li><strong>Type</strong>  : tout simplement le type de la ressource (vidéo, image, texte...)</li>
                    </ul>
                    <p>S'elle utilise un de ces paramètres, l'équipe pédagogique devra bien sûr avoir au préalable renseigné leurs valeurs pour chacune des ressources utilisées dans le cours (ou du moins pour celles qu'elle souhaite pouvoir désigner avec ces paramètres).</p>
                </li>
                <li><strong>Social</strong> : l'apprenant est invité à se rendre sur les réseaux sociaux, ou sur le forum du MOOC.</li>
                <li><strong>Exercice</strong> : cette activité est la réalisation d'un exercice par l'apprenant</li>
                <li><strong>Message</strong> : il ne s'agit pas d'une activité à proprement parler, mais se manifestera tout de même dans la boussole : il s'agit du simple affichage d'un message à destination de l'apprenant (pour lui dire bonjour, le féliciter, l'encourager,...).</li>
            </ul>
            
            <h4>...la stratégie pédagogique</h4>
            <p>C'est avec la stratégie pédagogique que l'équipe pédagogique va pouvoir exprimer, sous forme de règles, la manière dont elle souhaite personnaliser son MOOC à chacun des apprenants, en fonction des valeurs prises par les indicateurs dans son profil. Afin de bien comprendre la manière dont sont définies les règles, prenons l'exemple d'un cours de programmation en Python, qui contient deux ressources : une vidéo 'boucle for' et un quiz 'Quiz1' qui permet de tester les connaissances sur la boucle for. Supposons que l'équipe pédagogique, afin de tester le niveau des apprenants, leur mettre directement, dès la première séquence, le Quiz1 (sans leur montrer la vidéo). Le résultat à ce quiz remplit directement un indicateur dans la partie 'knowledge' du profil d'apprenant, l'indicateur 'RésultatBoucleFor'. Voici la règle que peut alors définir l'équipe pédagogique :</p>
            <p><strong>SI RésultatBoucleFor &lt; 60 ALORS regarder vidéo 'bouclefor'</strong>.</p>
            <p>La partie SINON étant optionnelle, nous ne l'avons pas fait figurer ici, mais on pourrait avoir :</p>
            <p><strong>SINON aller sur le FORUM avec Action = Answer</strong>, on invite ainsi l'apprenant à aller sur le forum, et répondre aux questions que se posent ceux qui n'ont éventuellement pas compris certaines parties du cours (bien évidemment, dans la boussole le tout sera sous forme textuelle et bien plus explicite). On pourrait aussi lui proposer de faire un exercice en relation avec la boucle for qui soit d'un niveau plus avancé, plus ludique (calcul des termes de la suite de Fibonacci,...)</p>
            <p>Voici donc comment se décomposent les règles de la stratégie pédagogique :</p>
            <p><strong>SI {Contrainte sur profil} ALORS {Activité avec paramètres} [SINON {Activité avec paramètres}]</strong>.</p>
            <p>A chacune des règles, l'équipe pédagogique peut attacher une valeur de priorité, nombre entier qui indique l'importance de cette règle. Ainsi, si plusieurs règles s'appliquent pour un apprenant, seules celles qui ont le plus haut degré de priorité seront prises en compte (et ce pour respecter les contraintes fixées dans le contexte de séquence).</p>
            <p>A partir de cette construction simple, l'équipe pédagogique a un champ de possibilités très large pour permettre aux apprenants d'avoir une boussole personnalisée, avec les activités qui leur convient au mieux.</p>
            
            <h4>Pour les plus courageux...</h4>
            <p>Pour ceux qui souhaiteraient avoir une vision complète de tous les modèles évoqués ici, voici tout simplement un  lien vers les différents fichiers XMLSchema qui nous ont permis de formaliser notre modèle :</p>
            <ul>
                <li><a href="resources/learnerMoocProfile.xsd">Modèle de profil d'apprenant</a></li>
                <li><a href="resources/context.xsd">Modèle de contexte</a> (qui regroupe contexte de séquence et contexte 'live')</li>
                <li><a href="resources/foveaPedagogicalProperties.xml">Modèle OKEP de base</a> (issu du modèle OKEP, et relativement simple à comprendre, reegroupant les activités et leurs paramètres)</li>
                
            </ul>
            
            Voici également un exemple de <a href="resources/Sequence1.xml">stratégie pédagogique</a>, stockée au format XML (consultez la source de la page si l'affichage n'est pas satisfaisant).
            
            <h3>Bilan</h3>
            <p>Tout ceci demande donc un gros effort de réflexion de la part de toute l'équipe de conception d'un MOOC, afin d'identifier quelles informations judicieuses permettront une personnalisation efficace. Bien entendu, tout cela doit se faire en collaboration avec les concepteurs de la plateforme, qui seront les plus à mêmes de savoir quelles informations peuvent ou non être extraites des traces générées par les apprenants durant leurs activités.</p>
            
            
            
            
            <h2 id="appli">Présentation de l'application</h2>
            <h3>Fonctionnalités actuelles</h3>
            <p>L'application que vous découvrez ici est en cours de développement, le descriptif qui suit est en date du 04/07/2014. N'hésitez pas à consulter les pages de l'application citées au fur et à mesure que vous lisez le descriptif.</p>
            <p>Une première interface permet à un membre de l'équipe pédagogique de modifier le contexte de séquence, pour chaque séquence du MOOC. Il lui est tout simplement possible de modifier les valeurs textuelles contenues dans le fichier XML qu'il est en train de visualiser. Au passage, comme pour tous les fichiers utilisés par l'équipe pédagogique, il lui est possible de les consulter, modifier, supprimer, dupliquer, renommer et créer (à partir d'un fichier vide).<br/>
            La deuxième interface, qui est l'interface principale pour l'équipe pédagogique, est celle qui va lui permettre l'édition de sa stratégie pédagogique pour chaque séquence du MOOC. Elle est décomposée en trois parties principales :</p>
            <ul>
                <li>Sur la gauche se trouvent deux onglets, contenant un exemple de profil d'apprenant et un exemple de contexte 'live'. C'est à partir de cette partie qu'il pourra sélectionner des indicateurs, et définir des contraintes dans le 'SI' des règles qu'il forme.</li>
                <li>Sur la droite se trouvent les activités qui sont disponibles sur la plateforme de MOOC, avec leurs différents paramètres. Ces éléments seront eux sélectionnables lorsque l'équipe pédagogique définit les parties 'ALORS' et 'SINON' de la règle.</li>
                <li>Au centre se trouve l'ensemble des règles déjà définies par l'équipe pédagogique dans le fichier ouvert (en bas, sous le titre 'Règles déjà définies'). Pour chacune d'entre elles, il est possible de supprimer, modifier ou dupliquer la règle. Un bouton en haut de cette partie permet également de créer une règle à partir d'un modèle vide. Une fois le mode édition lancé, il est possible de modifier toutes les parties de la règle de manière dynamique, grâce à une interface et des icônes facilement compréhensibles (du moins nous l'espérons). On peut ainsi définir de nouvelles contraintes, créer des contraintes complexes (en utilisant des opérateurs booléens), ajouter des activités et des paramètres pour ces activités. Tous les champs sont facilement éditables, et plusieurs fonctionnalités simples permettent à l'équipe pédagogique une manipulation plus aisée (par exemple, si un indicateur est utilisé dans une règle, un clic sur le nom de cet indicateur permettra de le localiser dans le profil ou le contexte "live", de même pour les activités et paramètres en opérant un simple survol).</li>
            </ul>
            
            <p>Enfin, deux interfaces (qui exploitent en fait exactement le même code que la page de définition du contexte de séquence), vont permettre à l'équipe pédagogique de définir des profils et contexte 'live' qu'elle pourra à terme utiliser pour tester sa stratégie pédagogique.</p>
            
            <p>Vous remarquerez une 5ème interface, concernant la définition des ressources, qui permettra à terme de définir les ressources et de renseigner leurs attributs...mais pour plus de facilité et de compréhension pour l'équipe pédagogique elle a besoin d'être un peu remaniée.</p>
            
            <p>Sur toutes les interfaces, la documentation disponible dans les modèles définis est affichée à l'équipe pédagogique (ce sont les petites icônes <span class="glyphicon glyphicon-info-sign"></span> - cliquer pour avoir la documentation, survoler pour avoir l'échelle uniquement). Les valeurs admises par les paramètres et indicateurs sont également affichées ainsi que, dans le cas des paramètres d'activités, la liste des valeurs que l'équipe pédagogique a effectivement utilisées lorsqu'elle a défini les ressources (cela lui permet par exemple de disposer de la liste de toutes les ressources). De plus, tous ces éléments sont cliquables lorsque l'équipe pédagogique doit remplir un formulaire dans la règle en cours de définition (le formulaire des directement rempli avec la valeur souhaitée).</p>
            
            <p><i>Note sur la définition des règles</i> : vous l'aurez remarqué, il est tout à fait possible de définir des règles qui n'ont aucun sens avec cette interface (partie 'SI' vide, indicateurs laissés seuls sans opérateur de comparaison,...) sans qu'aucun message d'alerte ne soit lancé. Cela n'est en effet pas une priorité : nous nous concentrons surtout actuellement sur le fait de rendre l'application entièrement fonctionnelle, quitte à faire peser dans un premier temps plus de responsabilités sur l'utilisateur, qui doit s'assurer lui-même que ce qu'il décrit a bien un sens. De plus, comme il est toujours possible de supprimer une règle qui serait mal définie, une contrainte mal exprimée,... l'utilisateur pourra toujours revenir en arrière et corriger ses éventuelles erreurs.</p>
            
            <p>Pour terminer, nous vous proposons une vidéo de démonstration pour l'utilisation de l'application, telle qu'elle est avancée au 04/07/2014 :</p>
            <iframe width="560" height="315" src="//www.youtube.com/embed/8UqVxA7Cl7E" frameborder="0" allowfullscreen></iframe>
            
            <h3>Perspectives d'évolution</h3>
            <p>Voici, de manière non ordonnée, les perspectives d'évolution pour ce plugin, qui vont être abordées dans les prochains jours :</p>
            <ul>
                <li>En cours de développement actuellement : le module qui permettra, à partir des données sur les apprenants et celles définies par l'équipe pédagogique, de générer la boussole.</li>
                <li>Une fois ce module implémenté, permettre à l'équipe pédagogique de réaliser des tests, à partir de profils et contextes dont il aura lui-même  défini le contenu.</li>
                <li>L'ajout de contraintes lorsque l'équipe pédagogique utilise les formulaires et définit des règles afin de minimiser les risques d'erreur.</li>
                <li>Une documentation plus avancée, à la fois pour les concepteurs de plateformes de MOOCs et les équipes pédagogiques</li>
                <li>Automatisation de certains traitements sur les fichiers XML (qui sont pour l'instant réalisés "à la main")</li>
                <li>Le but final sera de proposer l'application complète sous forme de plugin pour les plateformes de MOOC. Dans la mesure où la première plateforme visée est Claroline Connect, la technologie utilisée est Symfony.</li>
                
                <li>Le design pourrait aussi être amélioré...</li>
            </p> 
                
            <p>D'autres objectifs, à moyen ou long terme, concernent cette application : </p>
            <ul>
                <li>D'autres travaux sont réalisés dans le projet COAT actuellement, l'un ayant notamment pour but de construire un langage d'interrogation de traces pour les non-informaticiens. A terme, l'objectif est donc d'intégrer cet outil afin de permettre à l'équipe pédagogique de déterminer et de calculer ses propres indicateurs.</li>
                <li>Des générateurs d'exercice sont également développées (certains déjà disponibles) au sein du projet : un autre but à terme sera donc de pouvoir les paramétrer à partir des règles définies par l'équipe pédagogique.</li>
                <li>Des indicateurs plus poussés, notamment en termes d'activités collaboratives réalisées par les apprenants pourront également être intégrés. Ceux-ci demanderont une analyse plus poussée des traces.</li>
                <li>Actuellement, lorsqu'elle définit sa stratégie pédagogique, l'équipe pédagogique n'a pas vraiment d'aide extérieure, elle n'a pas de donnée concernant les activités réelles du MOOC. On pourrait donc lui proposer un tableau de bord lui fournissant des informations sur le comportement global des apprenants (comme par exemple la  moyenne, pour chaque indicateur du profil, de toutes les valeurs prises par les apprenants). De manière plus complexe, une idée que nous avons également est d'exploiter les traces laissées par tous les apprenants afin de détecter les éventuelles difficultés qu'ils rencontrent, et les faire remonter à l'équipe pédagogique ou trouver une solution de manière dynamique.</li>
            
            </ul>
            <hr/>
            <h5>Notes</h5>
            <p id="footnotePersua2">1. <a href="http://hal.archives-ouvertes.fr/docs/00/69/19/84/PDF/Lefevre-Marie-EIAH2011.pdf">Article PERSUA2</a></p>
        </div>
        <script type="text/javascript" src="js/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
	</body>
	
	
    
</html>