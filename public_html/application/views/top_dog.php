<div class="sub_header">Background</div>
<p>
The Top Dog handicap tournament system was an idea developed by Dean Bedlington of Olympian Games in Australia for DBM circa 2000 and later
supported in David Young&apos;s glicko ratings for DBM. It provides a way to run a parallel tournament at a wargames event where the prize or
prizes awarded are based on each player+army combination playing against their historical track record in competitions. Hence it is like
the handicap system in golf. It also aims to encourage army diversity by penalising armies commonly seen at tournaments and rewarding
those that have seen little use so far.
<p> 
These notes are based on David Young&apos;s notes on the Top Dog system hosted on the <a href="http://www.nasamw.org/notes_on_top_dog_handicapping.htm">
NASAMW Glicko page</a>.
<p> 
The Top Dog handicap is not based on Glicko ratings but takes advantage of the data collected and published for Glicko ratings.
 The handicaps are based on average score per game, for DBMM calculated on 0 to 25 basis. These "Top Dog" values are published 
 for each army and player on this site. 
 <div class="sub_header">Running a Top Dog Tournament</div>
<p> 
To run a Top Dog event you just run a normal wargames competition with scores, prizes etc awarded as usual but in addition the
Top Dog prize is awarded to the player with the highest normal score <b>minus</b> their Top Dog handicap multiplied by the number
of rounds in the competition. Normally anyone who finishes on the podium (under the normal scoring system) is excluded from 
winning the Top Dog event. Thus the system spreads prizes around and gives everyone something to aim for.
<p> 
The total Top Dog handicap <b> per round</b> is 2/3 of the player Top Dog value plus 1/3 of the army Top Dog value (Dean decided 
on these after doing some analysis on the predictive ability of different proportions).
<p>
If players miss a round or get a bye then that round&apos;s score does not count for Top Dog (tough luck).
<div class="sub_header">Details</div>
<p>
Generally the player handicap is equal to their historic average points per game but there is a discount for newer players to 
especially encourage them (bearing in mind that they are often realistically not in the running for other prizes), thus (where 
P is player average points per game under 0-25):
<p>
<pre>
No. of rated games Handicap
0 1.00
1 to 7 max( 1.00, P * 0.3333)
8 to 15 max( 1.00, P * 0.6667)
16 P
</pre>
<p>
The army handicap is based on the army&apos;s average score per game but with discounts for rarely used armies and penalties for over-used 
ones to encourage diversity of army usage, thus (where A is army average points per game under 0-25):
<p>
<pre>
No. of games for army Handicap
0 0.00
1 to 7 A * 0.25
8 to 15 A * 0.50
16 to 23 A * 0.75
24 to 99 A
100 to 199 A + 2
200 to 299 A + 4
300 to 399 A + 6
etc
</pre>
Note: the penalties for large numbers of games (>99) have been doubled to account for the change from 0-10 DBM scoring to 0-25 DBMM scoring.
<p>
So if I have played 12 rated games and have an average score of 4.50 per game and choose to use a virgin army, my handicap would be:
<p>
Player : 4.50 * 0.66667 = 3.00 Army: 0.00 Total: 2/3 * 3.00 + 1/3 * 0.00 = 2.00 per game
<p>
Which is pretty low and sees me in with a good chance of claiming the prize if I play well.
If however I was an experienced player of 50 games with an average score of 6 and used a popular army like LIR that had 250- games 
up and an average score of 5.5, my handicap would be:
<p>
Total = 2/3 * 6 + 1/3 *( 5.5 + 2) = 6.5 per game
<p>
Experienced average and better players sometimes took virgin or almost never used armies to a comp in the hope of picking up a Top Dog prize. 
The trick was to find such an army that wasn&apos;t actually a dog. David Young tried it with Mamlukes at Cancon a couple of years ago and got 
close (a new player did reasonably well and got the prize). Before that with Khwarizmians at MOAB with much less success.
<p>
Note the website Top Dog columns include all of the adjustments for number of games for players and armies.
<p>