# PDB2MovieWeb
Web-based front- and backend for PDB2Movie script

http://pdb2movie.warwick.ac.uk

This service uses low priority compute cycles at the Scientific Computing Research Technology Platform (https://warwick.ac.uk/research/rtp/sc) of the University of Warwick. The service is provided for free at the moment based on a low volume of such requests. 

If you wish to use the service more often, please get in touch as explained on our PDB2Movie web pages.

If you use the results of this calculation in a commercial and/or academic output (such as a patent or a paper), please
remember to acknowledge our two papers

[A] "Rapid simulation of protein motion: merging flexibility, rigidity and normal mode analyses", 
E. Jimenez-Roldan, R. Freedman, R. A. R&ouml;mer, S. A. Wells, 
Physical Biology 9, 016008-12 (2012), 
http://dx.doi.org/doi:10.1088/1478-3975/9/1/016008

[B] "The dynamics and flexibility of protein disulphide-isomerase (PDI): predictions of experimentally-observed domain motions", 
R. A. R&ouml;mer, S. A. Wells, J. E. Jimenez-Roldan, M. Bhattacharyya, S. Vishweshwara and R. B. Freedman, 
Proteins: Structure, Function and Bioinformatics 84, 1776-1785 (2016), 
http://dx.doi.org/10.1002/prot.25159

Updates:

- V1.2.0 23052018: 

Stable build of PDB2Movie based on PyMOL as video renders and using SC RTP pre-2018 desktop.

    * compute runs are still on "dovah" dedicated test queue, not yet full production
    * a fixed number of cores (16) is still used regardless of actual job size
