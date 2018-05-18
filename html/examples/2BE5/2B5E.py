# setting the overall representation
cmd.hide()
cmd.show('cartoon')

# coloring various bits of the protein

# domains
cmd.select( 'flexchain', 'resi 22-30' )
cmd.hide( 'cartoon', 'flexchain' )
cmd.select( 'a_domain', 'resi 31-140' )
cmd.color( 'blue', 'a_domain' )
cmd.select( 'b_domain', 'resi 141-240' )
cmd.color( 'green', 'b_domain' )
cmd.select( 'bprime_domain', 'resi 241-359' )
cmd.color( 'yellow', 'bprime_domain' )
cmd.select( 'x_linker', 'resi 360-374' )
cmd.color( 'black', 'x_linker' )
cmd.select( 'aprime_domain', 'resi 375-484' )
cmd.color( 'orange', 'aprime_domain' )
cmd.select( 'c_domain', 'resi 485-504' )
cmd.color( 'red', 'c_domain' )

# active sites
cmd.select( 'residue61', 'resi 61 and name ca')
cmd.color( 'yellow', 'residue61')
cmd.show( 'sphere', 'residue61' )
cmd.select( 'residue406', 'resi 406 and name ca')
cmd.color( 'yellow', 'residue406')
cmd.show( 'sphere', 'residue406' )
cmd.bg_color('white')

# view point
cmd.set_view( '-0.652014315,   -0.749999166,   -0.111257717,  -0.049654800,   -0.104185171,    0.993316829,   -0.756577671,    0.653182089,    0.030688595,    0.000000000,    0.000000000, -306.596191406,   -17.541816711,  -26.342689514,  -44.154975891,  241.722778320,  371.469604492,  -20.000000000' )

# DBG output (not needed)
print 'CARTOON rendering finished'

