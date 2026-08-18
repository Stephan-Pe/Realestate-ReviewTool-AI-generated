# Erstwohnsitz vs. Zweitwohnsitz

## Key-Takeaways & Einflussfaktoren

* Wertvorteil für Zweitwohnungen (+15 % bis +30 % oder mehr): In begehrten Schweizer Tourismusregionen (z. B. St. Moritz, Zermatt, Davos, Engadin) haben Bestandsimmobilien mit Zweitwohnsitzstatus oft einen spürbaren Marktwert-Aufschlag. Grund dafür ist das Zweitwohnungsgesetz (ZWG / Weber-Initiative): Da in Gemeinden mit über 20 % Zweitwohnungsanteil keine neuen Zweitwohnungen gebaut werden dürfen, ist das Angebot extrem verknappt, während die globale und nationale Nachfrage sehr hoch bleibt.  
* Erstwohnungen unterliegen Markt-Restriktionen: Wohnraum mit Erstwohnsitz-Auflage richtet sich nur an die lokale Bevölkerung oder Zuzüger. Die Nachfragegruppe ist viel kleiner und verfügt meist über geringere Budgets als internationale oder nationale Ferienhauskäufer, was den Marktwert pro Quadratmeter dämpft.
* Finanzierungsabschlag bei der Bank (Faktor Belehnung): Aus Sicht der Banken gelten für Ferienhäuser strengere Regeln, was die effektive Zahlungsbereitschaft und das Käuferfeld einschränkt:  

    * Eigenkapital: Bei Erstwohnsitz reichen 20 % (inkl. Geldern aus Säule 2/3a). Bei Feriendomizilen verlangen Banken 30 % bis 50 % reines Eigenkapital (Vorsorgegelder verboten).  
    * Amortisation: Feriendomizile müssen rascher amortisiert werden (oft auf 50 % bis 60 % des Marktwerts innerhalb von 15 Jahren).  

## Integration in bestehende Bewertung

1) Ergänzen der bestehenden Form id="valuationForm" durch select option mit den values Erstwohnsitz und Feriendomizil. 
2) Vor Button id="calcBtn" eine kurze Erklärung einfügen. "Erstwohnsitz: nach 2012 gebaut. Gesetzlich nur als Erstwohnsitz nutzbar. Feriendomizil: Wohnsitz ohne Auflagen, kann als Zweitwohnsitz genutzt werden. Erkundigen Sie sich bei Ihrem lokalen Bau- oder Grundbuchamt.
3) Werte zur Berechnung
    * Je höher der locations trend_factor, desto höher ist auch der Wert des Feriendomizils.
    * Zu erstellender Kalkulations-Faktor residence_status_factor mit den Werten 0.8, 1 und 1.4
    * Der residence_status_factor wird in der Berechnung verwendet, um den Wert des Feriendomizils zu erhöhen. 
    * Der residence_status_factor hat in der review_tool db eine eigene table mit den angegebenen Werten
    * Bei einer Bewertung eines Erstwohnsitz wird generell der residence_status_factor 0.8 angewendet, bei Feriendomizilen mit einem trend_factor von 1 wird auch der residence_status_factor 1 angewendet. Bei einem höheren trend_factor wird der residence_status_factor 1,4 angewendet.



4) 0.8 repräsentiert den Erstwohnsitz, 1 den Zweitwohnsitz und 1.4 den Zweitwohnsitz bei hohem trend_factor.

Passe die Kalkulation entsprechend an. Füge die residence_status_factor als Konstanten ein oder ergänze das review_tool.sql mit dem entsprechenden table.




